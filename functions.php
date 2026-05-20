<?php

/**
 * Child Theme Functions
 *
 * This file bootstraps the theme by including modular components.
 * Heavy logic lives in dedicated files under inc/.
 */

// Include modular components.
require_once __DIR__ . '/inc/post-types.php';
require_once __DIR__ . '/inc/carbon-fields-setup.php';
require_once __DIR__ . '/inc/video-project-shortcodes.php';
require_once __DIR__ . '/inc/project-rest-api.php';
require_once __DIR__ . '/inc/project-block.php';
require_once __DIR__ . '/inc/project-enqueue.php';
require_once __DIR__ . '/inc/project-grid-shortcode.php';
require_once __DIR__ . '/inc/featured-project-shortcode.php';
require_once __DIR__ . '/inc/logo-carousel-block.php';
require_once __DIR__ . '/inc/testimonials-slider-block.php';
require_once __DIR__ . '/inc/site-header-block.php';
require_once __DIR__ . '/inc/block-styles.php';
require_once __DIR__ . '/inc/redirects.php';
require_once __DIR__ . '/inc/breadcrumbs.php';

// Enqueue parent and child theme styles
function spectra_child_enqueue_parent_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style(
        'spectra-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/style.css')
    );
}
add_action('wp_enqueue_scripts', 'spectra_child_enqueue_parent_styles');

/**
 * Debug: Add body class to verify post type
 */
add_filter('body_class', 'spectra_child_body_class');
function spectra_child_body_class($classes) {
    if (is_singular('video-project')) {
        $classes[] = 'single-video-project';
        $classes[] = 'wp-embed-responsive';
    }
    return $classes;
}

/**
 * Ensure block theme compatibility for video-project CPT
 */
add_action('wp_enqueue_scripts', 'spectra_child_enqueue_block_styles', 20);
function spectra_child_enqueue_block_styles() {
    if (is_singular('video-project')) {
        wp_enqueue_style('global-styles');
    }
}

/**
 * Add 'plain-body-link' class to links without existing classes.
 * Runs on paragraph and heading blocks in single posts/pages.
 */
function add_class_to_plain_text_links_in_block( $block_content, $block ) {
    // Only process core/paragraph and core/heading blocks on single posts/pages
    if ( ! in_array( $block['blockName'], ['core/paragraph', 'core/heading'], true ) ) {
        return $block_content;
    }
    if ( ! ( is_single() || is_page() ) ) {
        return $block_content;
    }

    // Match any <a> tag that doesn't already have a class attribute
    $pattern = '/<a\s+((?![^>]*class\s*=)[^>]*)>(.*?)<\/a>/i';

    $block_content = preg_replace_callback( $pattern, function( $matches ) {
        return '<a ' . trim($matches[1]) . ' class="plain-body-link">' . $matches[2] . '</a>';
    }, $block_content );

    return $block_content;
}
add_filter( 'render_block', 'add_class_to_plain_text_links_in_block', 10, 2 );