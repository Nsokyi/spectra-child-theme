<?php
/**
 * Register the Logo Carousel Gutenberg block and conditionally enqueue assets.
 */

add_action('init', 'register_logo_carousel_block');

function register_logo_carousel_block() {
    register_block_type(get_stylesheet_directory() . '/blocks/logo-carousel');
}

add_action('wp_enqueue_scripts', 'enqueue_logo_carousel_assets');

function enqueue_logo_carousel_assets() {
    $should_load = has_block('spectra-child/logo-carousel');

    if (!$should_load) {
        $post = get_queried_object();
        if ($post instanceof WP_Post && has_shortcode($post->post_content, 'logo_carousel')) {
            $should_load = true;
        }
    }

    if (!$should_load) {
        return;
    }

    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();
    $css_path  = $theme_dir . '/assets/css/logo-carousel.css';

    wp_enqueue_style(
        'logo-carousel',
        $theme_uri . '/assets/css/logo-carousel.css',
        array(),
        file_exists($css_path) ? filemtime($css_path) : null
    );
}
