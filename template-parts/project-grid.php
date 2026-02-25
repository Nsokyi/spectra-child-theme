<?php
/**
 * Template Part: Filterable Project Grid
 *
 * Accepts $args:
 *   - featured_only (bool)  Show only featured projects
 *   - current_term  (array) Pre-selected filter: ['taxonomy' => 'service', 'slug' => 'brand']
 *   - per_page      (int)   Posts per page (default 12)
 */

$featured_only = !empty($args['featured_only']);
$current_term  = !empty($args['current_term']) ? $args['current_term'] : null;
$per_page      = !empty($args['per_page']) ? intval($args['per_page']) : 12;

$services   = get_terms(array('taxonomy' => 'service', 'hide_empty' => true));
$industries = get_terms(array('taxonomy' => 'industry', 'hide_empty' => true));

$query_args = array(
    'post_type'      => 'video-project',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'orderby'        => 'date',
    'order'          => 'DESC',
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
     data-per-page="<?php echo esc_attr($per_page); ?>">

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

    <?php if (!is_wp_error($services) && !empty($services)) : ?>
        <div class="project-filters project-filters--secondary<?php echo !empty($active_industry) ? ' is-visible' : ''; ?>" data-taxonomy="service" role="group" aria-label="<?php esc_attr_e('Filter by service', 'spectra-child'); ?>">
            <button class="project-filters__btn<?php echo empty($active_service) ? ' is-active' : ''; ?>"
                    data-slug=""
                    type="button">
                <?php esc_html_e('All Services', 'spectra-child'); ?>
            </button>
            <?php foreach ($services as $term) : ?>
                <button class="project-filters__btn<?php echo $active_service === $term->slug ? ' is-active' : ''; ?>"
                        data-slug="<?php echo esc_attr($term->slug); ?>"
                        type="button">
                    <?php echo esc_html($term->name); ?>
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="project-grid" aria-live="polite">
        <?php if ($projects->have_posts()) : ?>
            <?php while ($projects->have_posts()) : $projects->the_post(); ?>
                <?php
                $post_id       = get_the_ID();
                $thumbnail_url = get_the_post_thumbnail_url($post_id, 'medium_large');
                $custom_thumb  = carbon_get_post_meta($post_id, 'custom_thumbnail');
                if (!empty($custom_thumb)) {
                    $thumbnail_url = $custom_thumb;
                }
                $client = carbon_get_post_meta($post_id, 'client_name');
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
                        <h3 class="project-item__title"><?php the_title(); ?></h3>
                        <?php if ($client) : ?>
                            <span class="project-item__client"><?php echo esc_html($client); ?></span>
                        <?php endif; ?>
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

    <?php if ($projects->max_num_pages > 1) : ?>
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
