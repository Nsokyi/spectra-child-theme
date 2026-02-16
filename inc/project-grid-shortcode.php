<?php
/**
 * Shortcode: [project_grid]
 *
 * Renders the filterable project grid template part.
 * Automatically detects taxonomy archive context for pre-selected filters.
 *
 * Attributes:
 *   - featured (bool)  Show only featured projects. Default: false.
 *   - per_page (int)   Posts per page. Default: 12.
 */

add_shortcode('project_grid', 'render_project_grid_shortcode');

function render_project_grid_shortcode($atts) {
    $atts = is_array($atts) ? $atts : array();
    $atts = shortcode_atts(array(
        'featured' => false,
        'per_page' => 12,
    ), $atts);

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

    ob_start();
    get_template_part('template-parts/project-grid', null, array(
        'featured_only' => filter_var($atts['featured'], FILTER_VALIDATE_BOOLEAN),
        'per_page'      => intval($atts['per_page']),
        'current_term'  => $current_term,
    ));
    return ob_get_clean();
}
