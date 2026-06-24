<?php
/**
 * Shortcode: [project_featured]
 *
 * Renders the featured project hero section.
 * Displays the most recently published project marked as featured.
 */

add_shortcode('project_featured', 'spectra_child_render_featured_project_shortcode');

function spectra_child_render_featured_project_shortcode($atts) {
    $atts = shortcode_atts(array(), is_array($atts) ? $atts : array());

    $cache_key = 'vpe_featured_project_id';
    $post_id   = get_transient( $cache_key );

    if ( false === $post_id ) {
        $query = new WP_Query( array(
            'post_type'      => 'video-project',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => '_featured_project',
                    'value'   => 'yes',
                    'compare' => '=',
                ),
            ),
        ) );

        $post_id = ! empty( $query->posts ) ? (int) $query->posts[0] : 0;
        set_transient( $cache_key, $post_id, 12 * HOUR_IN_SECONDS );
    }

    if ( ! $post_id ) {
        return '';
    }

    ob_start();
    get_template_part( 'template-parts/featured-project', null, array(
        'project_id' => $post_id,
    ) );
    return ob_get_clean();
}
