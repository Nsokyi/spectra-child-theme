<?php
/**
 * Conditionally enqueue project grid assets.
 *
 * Loads CSS and JS only when the project grid block is present
 * or on taxonomy archive pages for service/industry.
 */

add_action('wp_enqueue_scripts', 'enqueue_project_grid_assets');

function enqueue_project_grid_assets() {
    $should_load = has_block('spectra-child/project-grid')
        || is_tax('service')
        || is_tax('industry')
        || is_post_type_archive('video-project');

    if (!$should_load) {
        return;
    }

    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();

    wp_enqueue_style(
        'project-grid',
        $theme_uri . '/assets/css/project-grid.css',
        array(),
        filemtime($theme_dir . '/assets/css/project-grid.css')
    );

    wp_enqueue_script(
        'project-filter',
        $theme_uri . '/assets/js/project-filter.js',
        array(),
        filemtime($theme_dir . '/assets/js/project-filter.js'),
        true
    );

    $current_term = null;
    if (is_tax('service') || is_tax('industry')) {
        $queried = get_queried_object();
        if ($queried) {
            $current_term = array(
                'taxonomy' => $queried->taxonomy,
                'slug'     => $queried->slug,
            );
        }
    }

    wp_localize_script('project-filter', 'projectFilterData', array(
        'restUrl'     => rest_url('project/v1/filter'),
        'nonce'       => wp_create_nonce('wp_rest'),
        'currentTerm' => $current_term,
    ));
}
