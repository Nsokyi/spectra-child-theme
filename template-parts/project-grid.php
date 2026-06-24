<?php
/**
 * Template Part: Filterable Project Grid
 *
 * Accepts $args:
 *   - featured_only   (bool)   Show only featured projects
 *   - current_term    (array)  Pre-selected filter: ['taxonomy' => 'service', 'slug' => 'brand']
 *   - per_page        (int)    Posts per page (default 6)
 *   - show_filter_bar (bool)   Whether to render filter buttons (default true)
 *   - show_load_more  (bool)   Whether to render the Load More button (default true)
 *   - columns         (int)    Grid columns: 2 or 3 (default 3)
 *   - orderby         (string) 'date' or 'menu_order' (default 'date')
 */

$featured_only   = !empty($args['featured_only']);
$current_term    = !empty($args['current_term']) ? $args['current_term'] : null;
$per_page        = !empty($args['per_page']) ? intval($args['per_page']) : 6;
$show_filter_bar = isset($args['show_filter_bar']) ? (bool) $args['show_filter_bar'] : true;
$show_service_row = isset($args['show_service_row']) ? (bool) $args['show_service_row'] : true;
$show_load_more  = isset($args['show_load_more']) ? (bool) $args['show_load_more'] : true;
$columns         = !empty($args['columns']) ? intval($args['columns']) : 3;
$orderby         = !empty($args['orderby']) && $args['orderby'] === 'menu_order' ? 'menu_order' : 'date';

$services   = get_terms(array('taxonomy' => 'service', 'hide_empty' => true));
$industries = get_terms(array('taxonomy' => 'industry', 'hide_empty' => true));

// When an industry is pre-selected, determine which services are available for cross-filtering.
$available_service_slugs = null;
if ($current_term && isset($current_term['taxonomy']) && $current_term['taxonomy'] === 'industry' && !empty($current_term['slug'])) {
    $industry_slug = sanitize_title($current_term['slug']);
    $cache_key     = 'vpe_avail_services_' . $industry_slug;
    $cached        = get_transient($cache_key);

    if (false !== $cached) {
        $available_service_slugs = $cached;
    } else {
        $industry_post_ids = get_posts(array(
            'post_type'      => 'video-project',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'industry',
                    'field'    => 'slug',
                    'terms'    => $industry_slug,
                ),
            ),
        ));
        if (!empty($industry_post_ids)) {
            $available_terms = wp_get_object_terms($industry_post_ids, 'service', array('fields' => 'slugs'));
            if (!is_wp_error($available_terms)) {
                $available_service_slugs = array_values(array_unique($available_terms));
            } else {
                $available_service_slugs = array();
            }
        } else {
            $available_service_slugs = array();
        }
        set_transient($cache_key, $available_service_slugs, 12 * HOUR_IN_SECONDS);
    }
}

$order = ($orderby === 'menu_order') ? 'ASC' : 'DESC';

$query_args = array(
    'post_type'      => 'video-project',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'orderby'        => $orderby,
    'order'          => $order,
);

if ($featured_only) {
    $query_args['meta_query'] = array(
        array(
            'key'     => '_featured_project',
            'value'   => 'yes',
            'compare' => '=',
        ),
    );
}

if ($current_term) {
    $allowed_taxonomies = array('service', 'industry');
    $taxonomy = isset($current_term['taxonomy']) ? sanitize_key($current_term['taxonomy']) : '';
    $slug = isset($current_term['slug']) ? sanitize_title($current_term['slug']) : '';

    if (in_array($taxonomy, $allowed_taxonomies, true) && !empty($slug)) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $slug,
            ),
        );
    }
}

$projects = new WP_Query($query_args);

$active_service  = $current_term && $current_term['taxonomy'] === 'service' ? $current_term['slug'] : '';
$active_industry = $current_term && $current_term['taxonomy'] === 'industry' ? $current_term['slug'] : '';
?>

<div class="project-filters-wrap"
     data-featured="<?php echo $featured_only ? '1' : ''; ?>"
     data-per-page="<?php echo esc_attr($per_page); ?>"
     data-show-load-more="<?php echo $show_load_more ? '1' : '0'; ?>"
     data-columns="<?php echo esc_attr($columns); ?>"
     data-orderby="<?php echo esc_attr($orderby); ?>"
     <?php if ($current_term && isset($current_term['taxonomy'], $current_term['slug'])) : ?>
     data-current-taxonomy="<?php echo esc_attr($current_term['taxonomy']); ?>"
     data-current-slug="<?php echo esc_attr($current_term['slug']); ?>"
     <?php endif; ?>>

    <?php if ($show_filter_bar) : ?>
        <?php if (!is_wp_error($industries) && !empty($industries)) : ?>
            <div class="project-filters project-filters--primary" data-taxonomy="industry" role="group" aria-label="<?php esc_attr_e('Filter by industry', 'spectra-child'); ?>">
                <button class="project-filters__btn<?php echo empty($active_industry) ? ' is-active' : ''; ?>"
                        data-slug=""
                        type="button">
                    <?php esc_html_e('All Projects', 'spectra-child'); ?>
                </button>
                <?php foreach ($industries as $term) : ?>
                    <button class="project-filters__btn<?php echo $active_industry === $term->slug ? ' is-active' : ''; ?>"
                            data-slug="<?php echo esc_attr($term->slug); ?>"
                            type="button">
                        <?php echo esc_html($term->name); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($show_service_row && !is_wp_error($services) && !empty($services)) : ?>
            <div class="project-filters project-filters--secondary<?php echo (!empty($active_industry) || !empty($active_service)) ? ' is-visible' : ''; ?>" data-taxonomy="service" role="group" aria-label="<?php esc_attr_e('Filter by service', 'spectra-child'); ?>">
                <button class="project-filters__btn<?php echo empty($active_service) ? ' is-active' : ''; ?>"
                        data-slug=""
                        type="button">
                    <?php esc_html_e('All Services', 'spectra-child'); ?>
                </button>
                <?php foreach ($services as $term) :
                    $is_unavailable = ($available_service_slugs !== null && !in_array($term->slug, $available_service_slugs, true));
                ?>
                    <button class="project-filters__btn<?php echo $active_service === $term->slug ? ' is-active' : ''; ?><?php echo $is_unavailable ? ' is-unavailable' : ''; ?>"
                            data-slug="<?php echo esc_attr($term->slug); ?>"
                            type="button">
                        <?php echo esc_html($term->name); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="project-grid" aria-live="polite">
        <?php if ($projects->have_posts()) : ?>
            <?php while ($projects->have_posts()) : $projects->the_post(); ?>
                <?php
                $post_id       = get_the_ID();
                $thumbnail_url = get_the_post_thumbnail_url($post_id, 'medium_large');
                $client        = carbon_get_post_meta($post_id, 'client_name');
                $item_services = get_the_terms($post_id, 'service');
                ?>
                <a href="<?php the_permalink(); ?>" class="project-item">
                    <figure class="project-item__image">
                        <?php if ($thumbnail_url) : ?>
                            <img src="<?php echo esc_url($thumbnail_url); ?>"
                                 alt="<?php the_title_attribute(); ?>"
                                 loading="lazy"
                                 width="640"
                                 height="360">
                        <?php else : ?>
                            <div class="project-item__placeholder"></div>
                        <?php endif; ?>
                    </figure>
                    <div class="project-item__meta">
                        <?php if ($client || ($item_services && !is_wp_error($item_services))) : ?>
                            <div class="project-item__tags">
                                <span class="project-item__client-tag"><?php echo $client ? esc_html($client) : '&nbsp;'; ?></span>
                                <?php if ($item_services && !is_wp_error($item_services)) : ?>
                                    <span class="project-item__service-tag"><?php echo esc_html($item_services[0]->name); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <h3 class="project-item__title"><?php the_title(); ?></h3>
                    </div>
                </a>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <div class="project-grid__empty">
                <p><?php esc_html_e('No projects found.', 'spectra-child'); ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="project-grid__loading" aria-hidden="true" hidden>
        <?php for ($i = 0; $i < 6; $i++) : ?>
            <div class="project-item project-item--skeleton">
                <div class="project-item__image"></div>
                <div class="project-item__meta">
                    <div class="project-item__title"></div>
                    <div class="project-item__client"></div>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <?php if ($show_load_more && $projects->max_num_pages > 1) : ?>
        <div class="project-grid__pagination">
            <button class="project-grid__load-more"
                    type="button"
                    data-page="1"
                    data-max="<?php echo esc_attr($projects->max_num_pages); ?>">
                <?php esc_html_e('Load More', 'spectra-child'); ?>
            </button>
        </div>
    <?php endif; ?>
</div>
