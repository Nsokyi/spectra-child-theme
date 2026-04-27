<?php
/**
 * Video Project Shortcodes
 *
 * All shortcodes that render video-project single-page sections:
 * [video_project_meta]         — Client, agency, featured badge, services, industries
 * [video_project_embed]        — Video iframe embed
 * [video_project_taxonomies]   — Service & industry taxonomy pills
 * [video_project_credits]      — Production crew credits grid
 * [video_project_testimonial]  — Star rating + client quote
 * [video_project_stills]       — Production stills gallery
 * [video_project_similar]      — Related projects grid
 * [year]                       — Current four-digit year (footer copyright)
 */

/**
 * Helper: Resolve post ID from shortcode attributes
 */
function spectra_child_resolve_video_project_id($atts) {
    $atts = is_array($atts) ? $atts : array();
    $atts = shortcode_atts(array('id' => ''), $atts);
    $post_id = !empty($atts['id']) ? intval($atts['id']) : get_the_ID();
    
    if (!$post_id || get_post_type($post_id) !== 'video-project') {
        return false;
    }
    
    return $post_id;
}

/**
 * Shortcode: Display Video Project Meta (Client, Agency, Featured Badge)
 */
add_shortcode('video_project_meta', 'spectra_child_render_video_project_meta');
function spectra_child_render_video_project_meta($atts) {
    $post_id = spectra_child_resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }
    
    $client_name    = carbon_get_post_meta($post_id, 'client_name');
    $agency         = carbon_get_post_meta($post_id, 'agency');
    $video_duration = carbon_get_post_meta($post_id, 'video_duration');
    $is_featured    = carbon_get_post_meta($post_id, 'featured_project');
    $services       = get_the_terms($post_id, 'service');
    $industries     = get_the_terms($post_id, 'industry');
    
    ob_start();
    ?>
    <aside class="video-project-meta">
        <?php if ($is_featured) : ?>
            <div class="featured-badge has-primary-background-color has-background">
                ⭐ Featured Project
            </div>
        <?php endif; ?>
        
        <dl class="project-meta__list">
            <?php if ($client_name) : ?>
                <div class="project-meta__item">
                    <dt class="has-neutral-color has-text-color">Client</dt>
                    <dd class="has-heading-color has-text-color"><?php echo esc_html($client_name); ?></dd>
                </div>
            <?php endif; ?>
            
            <?php if ($agency) : ?>
                <div class="project-meta__item">
                    <dt class="has-neutral-color has-text-color">Agency</dt>
                    <dd class="has-heading-color has-text-color"><?php echo esc_html($agency); ?></dd>
                </div>
            <?php endif; ?>
            
            <?php if ($video_duration) : ?>
                <div class="project-meta__item">
                    <dt class="has-neutral-color has-text-color">Duration</dt>
                    <dd class="has-heading-color has-text-color"><?php echo esc_html($video_duration); ?></dd>
                </div>
            <?php endif; ?>
            
            <?php if ($services && !is_wp_error($services)) : ?>
                <div class="project-meta__item">
                    <dt class="has-neutral-color has-text-color">Services</dt>
                    <dd class="has-heading-color has-text-color">
                        <?php echo esc_html(implode(', ', wp_list_pluck($services, 'name'))); ?>
                    </dd>
                </div>
            <?php endif; ?>
            
            <?php if ($industries && !is_wp_error($industries)) : ?>
                <div class="project-meta__item">
                    <dt class="has-neutral-color has-text-color">Industry</dt>
                    <dd class="has-heading-color has-text-color">
                        <?php echo esc_html(implode(', ', wp_list_pluck($industries, 'name'))); ?>
                    </dd>
                </div>
            <?php endif; ?>
        </dl>

        <a class="project-meta__cta wp-block-button__link has-primary-background-color has-background"
           href="<?php echo esc_url(home_url('/contact-us/')); ?>">
            <?php esc_html_e('Start Your Project', 'spectra-child'); ?>
        </a>
    </aside>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Display Video Embed
 */
add_shortcode('video_project_embed', 'spectra_child_render_video_project_embed');
function spectra_child_render_video_project_embed($atts) {
    $post_id = spectra_child_resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }
    
    $video_url = carbon_get_post_meta($post_id, 'video_url');
    $video_duration = carbon_get_post_meta($post_id, 'video_duration');
    
    if (!$video_url) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="video-project-embed">
        <div class="video-container">
            <iframe 
                src="<?php echo esc_url($video_url); ?>" 
                frameborder="0" 
                allow="autoplay; fullscreen; picture-in-picture" 
                allowfullscreen>
            </iframe>
        </div>
        <?php if ($video_duration) : ?>
            <p class="video-duration has-neutral-color has-text-color">
                Duration: <?php echo esc_html($video_duration); ?>
            </p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Display Services and Industries Taxonomies
 */
add_shortcode('video_project_taxonomies', 'spectra_child_render_video_project_taxonomies');
function spectra_child_render_video_project_taxonomies($atts) {
    $post_id = spectra_child_resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }
    
    $services = get_the_terms($post_id, 'service');
    $industries = get_the_terms($post_id, 'industry');

    if (is_wp_error($services)) $services = false;
    if (is_wp_error($industries)) $industries = false;
    
    if (!$services && !$industries) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="video-project-taxonomies wp-block-group has-surface-background-color has-background">
        <div class="taxonomy-grid">
            <?php if ($services) : ?>
                <div class="services-list">
                    <h3 class="has-heading-color has-text-color">Services</h3>
                    <ul>
                        <?php foreach ($services as $service) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_term_link($service)); ?>" 
                                   class="taxonomy-pill has-background-color has-background has-body-color has-text-color">
                                    <?php echo esc_html($service->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($industries) : ?>
                <div class="industries-list">
                    <h3 class="has-heading-color has-text-color">Industries</h3>
                    <ul>
                        <?php foreach ($industries as $industry) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_term_link($industry)); ?>" 
                                   class="taxonomy-pill has-background-color has-background has-body-color has-text-color">
                                    <?php echo esc_html($industry->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Display Production Credits
 */
add_shortcode('video_project_credits', 'spectra_child_render_video_project_credits');
function spectra_child_render_video_project_credits($atts) {
    $post_id = spectra_child_resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }
    
    $credits = carbon_get_post_meta($post_id, 'credits');
    
    if (!$credits || empty($credits)) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="video-project-credits wp-block-group">
        <h3 class="has-heading-color has-text-color">Production Credits</h3>
        <div class="credits-grid">
            <?php foreach ($credits as $credit) : ?>
                <?php
                $role = isset($credit['role']) ? $credit['role'] : '';
                $name = isset($credit['name']) ? $credit['name'] : '';
                if (empty($role) && empty($name)) continue;
                ?>
                <div class="credit-item has-surface-background-color has-background">
                    <strong class="has-neutral-color has-text-color">
                        <?php echo esc_html($role); ?>
                    </strong>
                    <span class="has-heading-color has-text-color">
                        <?php echo esc_html($name); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Display Client Testimonial
 *
 * Requires both testimonial_rating and testimonial_text to render.
 * Author name and photo are optional.
 */
add_shortcode('video_project_testimonial', 'spectra_child_render_video_project_testimonial');
function spectra_child_render_video_project_testimonial($atts) {
    $post_id = spectra_child_resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }

    $rating = carbon_get_post_meta($post_id, 'testimonial_rating');
    $text   = carbon_get_post_meta($post_id, 'testimonial_text');

    if (!$rating || !$text) {
        return '';
    }

    $rating = intval($rating);
    $author = carbon_get_post_meta($post_id, 'testimonial_author');
    $photo  = carbon_get_post_meta($post_id, 'testimonial_photo');

    $star_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
    $empty_star_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';

    ob_start();
    ?>
    <div class="project-testimonial wp-block-group">
        <div class="project-testimonial__stars" role="img" aria-label="<?php echo esc_attr(sprintf(__('%d out of 5 stars', 'spectra-child'), $rating)); ?>">
            <?php for ($i = 1; $i <= 5; $i++) : ?>
                <span class="project-testimonial__star<?php echo $i <= $rating ? ' is-filled' : ''; ?>">
                    <?php echo $i <= $rating ? $star_svg : $empty_star_svg; ?>
                </span>
            <?php endfor; ?>
        </div>

        <blockquote class="project-testimonial__quote">
            <p class="has-body-color has-text-color"><?php echo esc_html($text); ?></p>
        </blockquote>

        <?php if ($author || $photo) : ?>
            <div class="project-testimonial__author">
                <?php if ($photo) : ?>
                    <img class="project-testimonial__photo"
                         src="<?php echo esc_url($photo); ?>"
                         alt="<?php echo esc_attr($author ?: __('Client photo', 'spectra-child')); ?>"
                         width="48"
                         height="48"
                         loading="lazy">
                <?php endif; ?>
                <?php if ($author) : ?>
                    <span class="project-testimonial__name has-heading-color has-text-color"><?php echo esc_html($author); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Display Production Stills Gallery
 */
add_shortcode('video_project_stills', 'spectra_child_render_video_project_stills');
function spectra_child_render_video_project_stills($atts) {
    $post_id = spectra_child_resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }

    $stills = carbon_get_post_meta($post_id, 'production_stills');

    if (!$stills || empty($stills)) {
        return '';
    }

    ob_start();
    ?>
    <div class="project-stills wp-block-group">
        <h2 class="has-heading-color has-text-color">Production Stills</h2>
        <div class="project-stills__grid">
            <?php foreach ($stills as $image_id) : ?>
                <?php
                $img_url = wp_get_attachment_image_url($image_id, 'large');
                $img_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                $img_full = wp_get_attachment_image_url($image_id, 'full');
                if (!$img_url) continue;
                ?>
                <a class="project-stills__item" href="<?php echo esc_url($img_full); ?>" target="_blank" rel="noopener">
                    <img src="<?php echo esc_url($img_url); ?>"
                         alt="<?php echo esc_attr($img_alt ?: __('Production still', 'spectra-child')); ?>"
                         width="600"
                         height="400"
                         loading="lazy">
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Display Similar Projects
 *
 * Queries posts sharing the same service AND/OR industry taxonomies.
 */
add_shortcode('video_project_similar', 'spectra_child_render_video_project_similar');
function spectra_child_render_video_project_similar($atts) {
    $post_id = spectra_child_resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }

    $services   = wp_get_post_terms($post_id, 'service', array('fields' => 'ids'));
    $industries = wp_get_post_terms($post_id, 'industry', array('fields' => 'ids'));

    if (empty($services) && empty($industries)) {
        return '';
    }

    $tax_query = array('relation' => 'OR');

    if (!empty($services)) {
        $tax_query[] = array(
            'taxonomy' => 'service',
            'field'    => 'term_id',
            'terms'    => $services,
        );
    }

    if (!empty($industries)) {
        $tax_query[] = array(
            'taxonomy' => 'industry',
            'field'    => 'term_id',
            'terms'    => $industries,
        );
    }

    $similar = new WP_Query(array(
        'post_type'      => 'video-project',
        'posts_per_page' => 4,
        'post__not_in'   => array($post_id),
        'tax_query'      => $tax_query,
        'orderby'        => 'rand',
    ));

    if (!$similar->have_posts()) {
        return '';
    }

    ob_start();
    ?>
    <div class="project-similar wp-block-group">
        <h2 class="has-heading-color has-text-color">Similar Projects</h2>
        <div class="project-similar__grid">
            <?php while ($similar->have_posts()) : $similar->the_post(); ?>
                <a class="project-similar__item" href="<?php the_permalink(); ?>">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="project-similar__thumb">
                            <?php the_post_thumbnail('medium_large'); ?>
                        </div>
                    <?php endif; ?>
                    <h3 class="project-similar__title has-heading-color has-text-color"><?php the_title(); ?></h3>
                    <span class="project-similar__client has-neutral-color has-text-color">
                        <?php echo esc_html(carbon_get_post_meta(get_the_ID(), 'client_name')); ?>
                    </span>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}

/**
 * [year] shortcode — outputs the current four-digit year.
 * Usage in footer: © [year] Video Production Edinburgh
 */
add_shortcode('year', function () {
    return date('Y');
});
