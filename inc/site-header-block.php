<?php
/**
 * Register the Site Header Gutenberg block, menu location, and conditionally enqueue assets.
 */

require_once __DIR__ . '/class-vpe-nav-walker.php';
require_once __DIR__ . '/class-vpe-mobile-nav-walker.php';

/**
 * Force current-menu-item on the Projects nav item when on the video-project CPT archive.
 *
 * WordPress compares nav item URLs against the current URL including query params,
 * so /projects/?service=photography fails to match /projects/ and current-menu-item
 * is never set. This filter adds it back when is_post_type_archive('video-project') is true
 * and the nav item URL matches the CPT archive permalink.
 */
add_filter( 'nav_menu_css_class', 'spectra_child_projects_nav_active_class', 10, 3 );
function spectra_child_projects_nav_active_class( $classes, $item, $args ) {
    if ( is_admin() ) {
        return $classes;
    }
    if ( is_post_type_archive( 'video-project' ) ) {
        $archive_url = get_post_type_archive_link( 'video-project' );
        if ( $archive_url && rtrim( $item->url, '/' ) === rtrim( $archive_url, '/' ) ) {
            if ( ! in_array( 'current-menu-item', $classes, true ) ) {
                $classes[] = 'current-menu-item';
            }
        }
    }
    return $classes;
}

/**
 * Register the "Primary" menu location.
 */
add_action('after_setup_theme', 'spectra_child_register_nav_menus');
function spectra_child_register_nav_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'spectra-child'),
    ));
    add_theme_support('custom-logo', array(
        'height'      => 72,
        'width'       => 240,
        'flex-height' => true,
        'flex-width'  => true,
    ));
}

/**
 * Register the block and its assets.
 */
add_action('init', 'spectra_child_register_site_header_block');
function spectra_child_register_site_header_block() {
    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();

    $css_path = $theme_dir . '/assets/css/header.css';
    $js_path  = $theme_dir . '/assets/js/header.js';

    if (file_exists($css_path)) {
        wp_register_style(
            'site-header',
            $theme_uri . '/assets/css/header.css',
            array(),
            filemtime($css_path)
        );
    }

    if (file_exists($js_path)) {
        wp_register_script(
            'site-header',
            $theme_uri . '/assets/js/header.js',
            array(),
            filemtime($js_path),
            true
        );
    }

    register_block_type(get_stylesheet_directory() . '/blocks/site-header', array(
        'style'       => 'site-header',
        'view_script' => 'site-header',
    ));
}
