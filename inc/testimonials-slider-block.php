<?php
/**
 * Register the Testimonials Slider Gutenberg block and conditionally enqueue assets.
 */

add_action('init', 'spectra_child_register_testimonials_slider_block');

function spectra_child_register_testimonials_slider_block() {
    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();

    $css_path = $theme_dir . '/assets/css/testimonials-slider.css';
    $js_path  = $theme_dir . '/assets/js/testimonials-slider.js';

    if (file_exists($css_path)) {
        wp_register_style(
            'testimonials-slider',
            $theme_uri . '/assets/css/testimonials-slider.css',
            array(),
            filemtime($css_path)
        );
    }

    if (file_exists($js_path)) {
        wp_register_script(
            'testimonials-slider',
            $theme_uri . '/assets/js/testimonials-slider.js',
            array(),
            filemtime($js_path),
            true
        );
    }

    register_block_type(get_stylesheet_directory() . '/blocks/testimonials-slider', array(
        'style'           => 'testimonials-slider',
        'view_script'     => 'testimonials-slider',
    ));
}
