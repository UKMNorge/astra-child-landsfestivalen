<?php

require_once('vendor/autoload.php');

function your_child_theme_enqueue_styles() {
    // Load parent theme styles
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'your_child_theme_enqueue_styles');


function enqueue_styles() {
    wp_enqueue_style('UKMFestivalenArrSysStyle', '//assets.' . UKM_HOSTNAME . '//css/arr-sys.css');
    wp_enqueue_style('UKMFestivalenSideStyle', '/wp-content/themes/astra-child-theme/styles/landsfestivalen.css');
	wp_enqueue_style('UKMFestivalenSideMIDIcons', 'https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css');

}
add_action('wp_enqueue_scripts', 'enqueue_styles');


// [landsfestivalen_shortcode component="deltakere"]
function vue_component_shortcode($atts) {
    enqueue_shortcodes_script();

    $atts = shortcode_atts(['component' => 'DefaultComponent'], $atts);
    return '<div class="vue-app" data-vue-component="' . esc_attr($atts['component']) . '"></div>';
}
add_shortcode('landsfestivalen_shortcode', 'vue_component_shortcode');



function enqueue_shortcodes_script() {
    wp_enqueue_style('UKMFestivalenSideBootstrap4', 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css');

    wp_enqueue_style('UKMFestivalenSideVueStyle', '/wp-content/themes/astra-child-theme/client/dist/assets/build.css');
    wp_enqueue_script('UKMFestivalenSideStyleVueJs', '/wp-content/themes/astra-child-theme/client/dist/assets/build.js', array(), '', true);
}

// Remove Astra main menu item from admin menu
add_action('admin_menu', function () {
    remove_menu_page('astra'); // Astra main menu
}, 999);

//
function register_custom_post_types() {

    register_post_type('innlegg', array(
        'labels' => array(
            'name' => 'Innlegg på nettsiden',
            'singular_name' => 'Innlegg'
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-format-aside',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'innlegg'),
        'show_in_rest' => true,
        'taxonomies' => array('category'),
    ));
}
add_action('init', 'register_custom_post_types');



function infosak_allowed_category_slugs(): array {
    return array('innlegg-info-til-deltakere', 'innlegg-info-til-publikum', 'innlegg-info-til-reiseleder');
}

/**
 * Create the allowed categories if they don't already exist.
 */
function infosak_ensure_categories_exist() {
    $categories = array(
        'innlegg-info-til-deltakere' => 'Info til deltakere',
        'innlegg-info-til-publikum'  => 'Info til publikum',
        'innlegg-info-til-reiseleder' => 'Info til reiseleder',
    );

    foreach ($categories as $slug => $name) {
        if (!get_term_by('slug', $slug, 'category')) {
            wp_insert_term($name, 'category', array('slug' => $slug));
        }
    }
}
add_action('init', 'infosak_ensure_categories_exist');

/**
 * Resolve allowed slugs to term IDs (cached).
 */
function infosak_allowed_category_ids(): array {
    static $ids = null;
    if ($ids !== null) {
        return $ids;
    }

    $ids = array();
    foreach (infosak_allowed_category_slugs() as $slug) {
        $term = get_term_by('slug', $slug, 'category');
        if ($term && !is_wp_error($term)) {
            $ids[] = (int) $term->term_id;
        }
    }

    // Ensure unique, non-empty, numeric
    $ids = array_values(array_unique(array_filter($ids)));
    return $ids;
}

/**
 * Show only allowed categories in the category checklist when editing infosak
 */
add_filter('wp_terms_checklist_args', function ($args, $post_id) {
    $post = get_post($post_id);
    if ($post && $post->post_type === 'infosak') {
        $allowed_ids = infosak_allowed_category_ids();

        // If none resolved (misconfiguration), show none rather than everything
        $args['include'] = $allowed_ids ?: array(-1);
    }
    return $args;
}, 10, 2);

/**
 * Server-side enforcement: remove any non-allowed categories on save
 * Optionally assigns the first allowed category if none selected.
 */
add_action('save_post_infosak', function ($post_id, $post, $update) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $allowed = infosak_allowed_category_ids();
    if (empty($allowed)) {
        // Fail closed: if slugs are wrong/missing, don't allow random categories
        wp_set_post_terms($post_id, array(), 'category', false);
        return;
    }

    $assigned = wp_get_post_terms($post_id, 'category', array('fields' => 'ids'));

    if (empty($assigned)) {
        wp_set_post_terms($post_id, array($allowed[0]), 'category', false);
        return;
    }

    $filtered = array_values(array_intersect($assigned, $allowed));
    if (empty($filtered)) {
        $filtered = array($allowed[0]);
    }

    if ($filtered !== $assigned) {
        wp_set_post_terms($post_id, $filtered, 'category', false);
    }
}, 10, 3);

/**
 * Add 3 submenu items under "Deltakerinfo" in admin, each filtered to one category.
 * Clicking them shows the infosak list filtered by that category.
 */
/**
 * The Innlegg menu links to the unfiltered list. Open Info til deltakere instead.
 */
add_action('load-edit.php', function () {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        return;
    }
    if (($_GET['post_type'] ?? '') !== 'innlegg' || !empty($_GET['category_name'])) {
        return;
    }

    $extra = $_GET;
    unset($extra['post_type'], $extra['mode']);
    if (!empty($extra)) {
        return;
    }

    wp_safe_redirect(admin_url('edit.php?post_type=innlegg&category_name=innlegg-info-til-deltakere'));
    exit;
});

add_action('admin_menu', function () {
    $allowed = infosak_allowed_category_ids();

    foreach ($allowed as $cat_id) {
        $term = get_term($cat_id, 'category');
        if (!$term || is_wp_error($term)) {
            continue;
        }

        add_submenu_page(
            'edit.php?post_type=innlegg',
            $term->name,
            $term->name,
            'edit_posts',
            'edit.php?post_type=innlegg&category_name=' . $term->slug
        );
    }
});




/**
 * Highlight correct submenu item when filtering by category in admin list.
 * Works both when clicking submenu links and when using the category dropdown filter.
 */
add_filter('submenu_file', function ($submenu_file) {
    global $typenow;

    // Change 'infosak' to your post type slug if needed
    if ($typenow === 'innlegg' && !empty($_GET['category_name'])) {
        $submenu_file = 'edit.php?post_type=innlegg&category_name=' . sanitize_text_field($_GET['category_name']);
    }

    return $submenu_file;
});

add_filter('parent_file', function ($parent_file) {
    global $typenow;

    if ($typenow === 'innlegg') {
        $parent_file = 'edit.php?post_type=innlegg';
    }

    return $parent_file;
});





/**
 * 1) When viewing the list filtered by category, make "Add New Post" keep that category.
 * 2) On the new-post screen, pre-assign that category automatically.
 */

/**
 * A) Modify the "Add New Post" button on the list screen to include category_name
 */
add_action('admin_head-edit.php', function () {
    $screen = get_current_screen();
    if (!$screen) return;

    // Only for your post type
    if ($screen->post_type !== 'innlegg') return;

    if (empty($_GET['category_name'])) return;

    $cat_slug = sanitize_text_field($_GET['category_name']);
    $new_url  = add_query_arg([
        'post_type'      => 'innlegg',
        'category_name'  => $cat_slug,
    ], admin_url('post-new.php'));

    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.querySelector('a.page-title-action'); // "Add New Post"
            if (btn) {
                btn.setAttribute('href', <?php echo json_encode($new_url); ?>);
            }
        });
    </script>
    <?php
});


/**
 * B) Preselect / assign the category on the "Add New" screen (sets term on auto-draft)
 */
add_action('admin_head-post-new.php', function () {
    $screen = get_current_screen();
    if (!$screen) return;

    if ($screen->post_type !== 'innlegg') return;
    if (empty($_GET['category_name'])) return;

    $cat_slug = sanitize_text_field($_GET['category_name']);
    $term = get_term_by('slug', $cat_slug, 'category');
    if (!$term || is_wp_error($term)) return;

    // On post-new.php WP has already created an auto-draft post at this point
    global $post;
    if (!$post || empty($post->ID)) return;

    // Assign the category (this makes it selected automatically in editor)
    wp_set_post_terms($post->ID, [(int) $term->term_id], 'category', false);
});




/**
 * Remove default submenu items under the custom post type menu (innlegg)
 */
add_action('admin_menu', function () {
    // Remove "Innlegg" (the list)
    remove_submenu_page('edit.php?post_type=innlegg', 'edit.php?post_type=innlegg');

    // Remove "Add New Post"
    remove_submenu_page('edit.php?post_type=innlegg', 'post-new.php?post_type=innlegg');

    $parent_slug = 'edit.php?post_type=innlegg';

    global $submenu;

    if (!isset($submenu[$parent_slug])) {
        return;
    }

    foreach ($submenu[$parent_slug] as $index => $item) {
        // Look for the taxonomy=category link
        if (strpos($item[2], 'taxonomy=category') !== false) {
            unset($submenu[$parent_slug][$index]);
        }
    }
}, 999);

