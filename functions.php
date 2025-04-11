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