<?php

require_once('vendor/autoload.php');

function your_child_theme_enqueue_styles() {
    // Load parent theme styles
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'your_child_theme_enqueue_styles');


// [landsfestivalen_shortcode component="deltakere"]
function vue_component_shortcode($atts) {
    echo 'Hello from vue_component_shortcode: ' . $atts['component'];
    enqueue_api_script();

    $atts = shortcode_atts(['component' => 'DefaultComponent'], $atts);
    return '<div class="vue-app" data-vue-component="' . esc_attr($atts['component']) . '"></div>';
}
add_shortcode('landsfestivalen_shortcode', 'vue_component_shortcode');



function enqueue_api_script() {
    wp_enqueue_style('UKMFestivalenSideVueStyle', '/wp-content/themes/astra-child-theme/client/dist/assets/build.css');
    wp_enqueue_script('UKMFestivalenSideStyleVueJs', '/wp-content/themes/astra-child-theme/client/dist/assets/build.js', array(), '', true);
}