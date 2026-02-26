<?php
/**
 * REST API Endpoint for Video Project Filtering
 *
 * Route: /wp-json/project/v1/filter
 * Method: GET
 * Parameters:
 *   - service  (comma-separated slugs)
 *   - industry (comma-separated slugs)
 *   - featured (1 or 0)
 *   - paged    (page number, default 1)
 *   - per_page (posts per page, default 12)
 */

add_action('rest_api_init', 'register_project_filter_endpoint');

function register_project_filter_endpoint() {
    register_rest_route('project/v1', '/filter', array(
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'handle_project_filter_request',
        'permission_callback' => '__return_true',
        'args'                => array(
            'service' => array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            ),
            'industry' => array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            ),
            'featured' => array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            ),
            'paged' => array(
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'default'           => 1,
            ),
            'per_page' => array(
                'type'              => 'integer',
                'sanitize_callback' => 'absint',
                'default'           => 12,
            ),
        ),
    ));
}

function handle_project_filter_request($request) {
    $service_slugs  = $request->get_param('service');
    $industry_slugs = $request->get_param('industry');
    $featured       = $request->get_param('featured');
    $paged          = $request->get_param('paged');
    $per_page       = min($request->get_param('per_page'), 48);

    $cache_key = 'project_filter_' . md5(wp_json_encode($request->get_params()));
    $cached    = get_transient($cache_key);

    if ($cached !== false) {
        return rest_ensure_response($cached);
    }

    $query_args = array(
        'post_type'      => 'video-project',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
    );

    $tax_query = array();

    if (!empty($service_slugs)) {
        $tax_query[] = array(
            'taxonomy' => 'service',
            'field'    => 'slug',
            'terms'    => array_map('sanitize_title', explode(',', $service_slugs)),
            'operator' => 'IN',
        );
    }

    if (!empty($industry_slugs)) {
        $tax_query[] = array(
            'taxonomy' => 'industry',
            'field'    => 'slug',
            'terms'    => array_map('sanitize_title', explode(',', $industry_slugs)),
            'operator' => 'IN',
        );
    }

    if (count($tax_query) > 1) {
        $tax_query['relation'] = 'AND';
    }

    if (!empty($tax_query)) {
        $query_args['tax_query'] = $tax_query;
    }

    if ($featured === '1') {
        $query_args['meta_query'] = array(
            array(
                'key'     => '_featured_project',
                'value'   => 'yes',
                'compare' => '=',
            ),
        );
    }

    $query = new WP_Query($query_args);

    $projects = array();
    foreach ($query->posts as $post_id) {
        $projects[] = format_project_for_rest($post_id);
    }

    $response = array(
        'projects'    => $projects,
        'total'       => $query->found_posts,
        'total_pages' => $query->max_num_pages,
        'current_page' => $paged,
    );

    set_transient($cache_key, $response, 12 * HOUR_IN_SECONDS);

    return rest_ensure_response($response);
}

/**
 * Format a single video project post for the REST response.
 */
function format_project_for_rest($post_id) {
    $thumbnail_id  = get_post_thumbnail_id($post_id);
    $thumbnail_url = $thumbnail_id ? wp_get_attachment_image_url($thumbnail_id, 'medium_large') : '';

    $services   = get_the_terms($post_id, 'service');
    $industries = get_the_terms($post_id, 'industry');

    return array(
        'id'         => $post_id,
        'title'      => get_the_title($post_id),
        'permalink'  => get_permalink($post_id),
        'thumbnail'  => $thumbnail_url,
        'video_url'  => carbon_get_post_meta($post_id, 'video_url'),
        'client'     => carbon_get_post_meta($post_id, 'client_name'),
        'featured'   => (bool) carbon_get_post_meta($post_id, 'featured_project'),
        'services'   => !is_wp_error($services) && $services ? array_map(function ($term) {
            return array('slug' => $term->slug, 'name' => $term->name);
        }, $services) : array(),
        'industries' => !is_wp_error($industries) && $industries ? array_map(function ($term) {
            return array('slug' => $term->slug, 'name' => $term->name);
        }, $industries) : array(),
    );
}

/**
 * Invalidate project filter transients when content changes.
 */
add_action('save_post_video-project', 'invalidate_project_filter_cache');
add_action('delete_post', 'invalidate_project_filter_cache_on_delete');
add_action('wp_trash_post', 'invalidate_project_filter_cache_on_delete');
add_action('edited_service', 'invalidate_project_filter_cache');
add_action('edited_industry', 'invalidate_project_filter_cache');
add_action('created_service', 'invalidate_project_filter_cache');
add_action('created_industry', 'invalidate_project_filter_cache');
add_action('delete_service', 'invalidate_project_filter_cache');
add_action('delete_industry', 'invalidate_project_filter_cache');

function invalidate_project_filter_cache() {
    global $wpdb;
    $wpdb->query(
        "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_project_filter_%' OR option_name LIKE '_transient_timeout_project_filter_%'"
    );
}

function invalidate_project_filter_cache_on_delete($post_id) {
    if (get_post_type($post_id) === 'video-project') {
        invalidate_project_filter_cache();
    }
}
