<?php

require_once('vendor/autoload.php');

function your_child_theme_enqueue_styles() {
    // Load parent theme styles
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'your_child_theme_enqueue_styles');


function custom_elementor_shortcode() {
    enqueue_api_script();
    return '<div id="vue-app">Hello from Vue Shortcode!</div>'; // Element where the Vue app will be mounted
}

add_shortcode('my_shortcode_ls', 'custom_elementor_shortcode');


function enqueue_api_script() {
    wp_enqueue_style('UKMFestivalenSideVueStyle', '/wp-content/themes/astra-child-theme/client/dist/assets/build.css');
    wp_enqueue_script('UKMFestivalenSideStyleVueJs', '/wp-content/themes/astra-child-theme/client/dist/assets/build.js', array(), '', true);
}