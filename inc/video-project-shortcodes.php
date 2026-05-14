<?php
/**
 * Video Project Shortcodes
 *
 * All shortcodes that render video-project single-page sections:
 * [video_project_content]      — Brief / Approach / Outcome sections
 * [video_project_meta]         — Sticky sidebar: client, industry, services, duration, delivered, CTA
 * [video_project_embed]        — Video iframe embed
 * [video_project_testimonial]  — Star rating + client quote
 * [video_project_stills]       — Production stills gallery
 * [video_project_similar]      — Related projects grid
 * [video_project_taxonomies]   — Deprecated (stub, returns empty)
 * [video_project_credits]      — Deprecated (stub, returns empty)
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
 * Shortcode: Display Brief / Approach / Outcome content sections
 */
add_shortcode('video_project_content', 'spectra_child_render_video_project_content');
function spectra_child_render_video_project_content($atts) {
    $post_id = spectra_child_resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }

    $brief    = carbon_get_post_meta($post_id, 'project_brief');
    $approach = carbon_get_post_meta($post_id, 'project_approach');
    $outcome  = carbon_get_post_meta($post_id, 'project_outcome');

    if (!$brief && !$approach && !$outcome) {
        return '';
    }

    ob_start();
    ?>
    <div class="project-content">
        <?php if ($brief) : ?><div class="project-content__section project-content__brief"><span class="project-content__kicker uppercase">// Overview</span><h2 class="project-content__heading has-heading-color has-text-color">The Brief</h2><div class="project-content__body"><?php echo wp_kses_post($brief); ?></div></div><?php endif; ?>
        <?php if ($approach) : ?><hr class="project-content__divider"><div class="project-content__section project-content__approach"><h2 class="project-content__heading has-heading-color has-text-color">The Approach</h2><div class="project-content__body"><?php echo wp_kses_post($approach); ?></div></div><?php endif; ?>
        <?php if ($outcome) : ?><div class="project-content__section project-content__outcome"><h2 class="project-content__heading">The Outcome</h2><div class="project-content__body"><?php echo wp_kses_post($outcome); ?></div><a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="vpe-text-link"><?php esc_html_e('Discuss your project', 'spectra-child'); ?></a></div><?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Display Project Sidebar Meta + CTA
 *
 * Shows: Client, Industry, Services, Duration, Delivered, CTA block.
 * CTA uses per-project values with global Options Page fallback.
 */
add_shortcode('video_project_meta', 'spectra_child_render_video_project_meta');
function spectra_child_render_video_project_meta($atts) {
    $post_id = spectra_child_resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }

    $client_name = carbon_get_post_meta($post_id, 'client_name');
    $industries  = get_the_terms($post_id, 'industry');
    $services    = get_the_terms($post_id, 'service');
    $duration    = carbon_get_post_meta($post_id, 'project_duration');
    $delivered   = carbon_get_post_meta($post_id, 'delivered_date');

    if (is_wp_error($industries)) $industries = false;
    if (is_wp_error($services))   $services   = false;

    $cta_heading = carbon_get_post_meta($post_id, 'sidebar_cta_heading');
    $cta_text    = carbon_get_post_meta($post_id, 'sidebar_cta_text');
    $cta_label   = carbon_get_post_meta($post_id, 'sidebar_cta_button_label');
    $cta_url     = carbon_get_post_meta($post_id, 'sidebar_cta_button_url');

    if (!$cta_heading) $cta_heading = carbon_get_theme_option('default_cta_heading') ?: __('Planning a similar project?', 'spectra-child');
    if (!$cta_text)    $cta_text    = carbon_get_theme_option('default_cta_text') ?: '';
    if (!$cta_label)   $cta_label   = carbon_get_theme_option('default_cta_button_label') ?: __('Start your project', 'spectra-child');
    if (!$cta_url)     $cta_url     = carbon_get_theme_option('default_cta_button_url') ?: '/contact-us/';

    ob_start();
    ?>
    <aside class="project-sidebar">
        <dl class="project-sidebar__meta">
            <?php if ($client_name) : ?><div class="project-sidebar__item"><dt>Client:</dt><dd><?php echo esc_html($client_name); ?></dd></div><?php endif; ?>
            <?php if ($industries) : ?><div class="project-sidebar__item"><dt>Industry:</dt><dd><?php echo esc_html(implode(', ', wp_list_pluck($industries, 'name'))); ?></dd></div><?php endif; ?>
            <?php if ($services) : ?><div class="project-sidebar__item"><dt>Services:</dt><dd><?php echo esc_html(implode(', ', wp_list_pluck($services, 'name'))); ?></dd></div><?php endif; ?>
            <?php if ($duration) : ?><div class="project-sidebar__item"><dt>Duration:</dt><dd><?php echo esc_html($duration); ?></dd></div><?php endif; ?>
            <?php if ($delivered) : ?><div class="project-sidebar__item"><dt>Delivered:</dt><dd><?php echo esc_html($delivered); ?></dd></div><?php endif; ?>
        </dl>
        <div class="project-sidebar__cta">
            <h3 class="project-sidebar__cta-heading"><?php echo esc_html($cta_heading); ?></h3>
            <?php if ($cta_text) : ?><p class="project-sidebar__cta-text"><?php echo esc_html($cta_text); ?></p><?php endif; ?>
            <a href="<?php echo esc_url(home_url($cta_url)); ?>" class="project-sidebar__cta-btn"><?php echo esc_html($cta_label); ?></a>
        </div>
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
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Deprecated: Taxonomy pills (services/industries now in sidebar)
 * Kept as stub to avoid errors if shortcode is still referenced in content.
 */
add_shortcode('video_project_taxonomies', 'spectra_child_render_video_project_taxonomies');
function spectra_child_render_video_project_taxonomies($atts) {
    return '';
}

/**
 * Deprecated: Production credits (removed from new design)
 * Kept as stub to avoid errors if shortcode is still referenced in content.
 */
add_shortcode('video_project_credits', 'spectra_child_render_video_project_credits');
function spectra_child_render_video_project_credits($atts) {
    return '';
}

/**
 * Shortcode: Display Client Testimonial
 *
 * Two-column editorial layout: kicker + author info (left), blockquote (right).
 * Requires testimonial_text to render. Author name/role are optional.
 */
add_shortcode('video_project_testimonial', 'spectra_child_render_video_project_testimonial');
function spectra_child_render_video_project_testimonial($atts) {
    $post_id = spectra_child_resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }

    $text = carbon_get_post_meta($post_id, 'testimonial_text');
    if (!$text) {
        return '';
    }

    $author = carbon_get_post_meta($post_id, 'testimonial_author');
    $role   = carbon_get_post_meta($post_id, 'testimonial_author_role');
    $rating = carbon_get_post_meta($post_id, 'testimonial_rating');

    $left  = '<div class="project-testimonial__meta">';
    $left .= '<span class="project-testimonial__kicker uppercase">// Client Feedback</span>';
    if ($author) {
        $left .= '<strong class="project-testimonial__name">' . esc_html($author) . '</strong>';
    }
    if ($role) {
        $left .= '<span class="project-testimonial__role">' . esc_html($role) . '</span>';
    }
    if ($rating && intval($rating) > 0) {
        $left .= '<span class="project-testimonial__stars" aria-label="' . esc_attr($rating) . ' out of 5 stars">' . str_repeat('&#9733;', intval($rating)) . '</span>';
    }
    $left .= '</div>';

    $right = '<blockquote class="project-testimonial__quote"><p>&ldquo;' . esc_html($text) . '&rdquo;</p></blockquote>';

    return '<div class="project-testimonial">' . $left . $right . '</div>';
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

    $items = '';
    foreach ($stills as $image_id) {
        $img_url = wp_get_attachment_image_url($image_id, 'large');
        $img_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        $img_full = wp_get_attachment_image_url($image_id, 'full');
        if (!$img_url) continue;
        $alt = esc_attr($img_alt ?: __('Production still', 'spectra-child'));
        $items .= '<figure class="project-stills__item" role="button" tabindex="0" data-full="' . esc_url($img_full) . '" data-thumb="' . esc_url($img_url) . '"><img src="' . esc_url($img_url) . '" alt="' . $alt . '" width="600" height="400" loading="lazy"><span class="project-stills__close" aria-label="' . esc_attr__('Close', 'spectra-child') . '">&times;</span></figure>';
    }

    $script = '<script>document.addEventListener("click",function(e){var item=e.target.closest(".project-stills__item");if(!item)return;var grid=item.closest(".project-stills__grid");if(!grid)return;var isExpanded=item.classList.contains("is-expanded");grid.querySelectorAll(".project-stills__item.is-expanded").forEach(function(el){el.classList.remove("is-expanded");var img=el.querySelector("img");if(el.dataset.thumb)img.src=el.dataset.thumb;});if(!isExpanded){item.classList.add("is-expanded");var img=item.querySelector("img");if(item.dataset.full)img.src=item.dataset.full;item.scrollIntoView({behavior:"smooth",block:"nearest"});}});</script>';

    return '<div class="project-stills wp-block-group"><h2 class="has-heading-color has-text-color">Production Stills</h2><div class="project-stills__grid">' . $items . '</div></div>' . $script;
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
        'post_status'    => 'publish',
        'posts_per_page' => 4,
        'post__not_in'   => array($post_id),
        'tax_query'      => $tax_query,
        'orderby'        => 'rand',
    ));

    if (!$similar->have_posts()) {
        return '';
    }

    $items = '';
    while ($similar->have_posts()) {
        $similar->the_post();
        $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
        $thumb = $thumb_url ? '<span class="project-similar__thumb"><img src="' . esc_url($thumb_url) . '" alt="' . esc_attr(get_the_title()) . '" loading="lazy"></span>' : '';
        $title = '<span class="project-similar__title">' . esc_html(get_the_title()) . '</span>';
        $industry_terms = get_the_terms(get_the_ID(), 'industry');
        $industry_label = (!is_wp_error($industry_terms) && $industry_terms) ? esc_html($industry_terms[0]->name) : '';
        $industry = $industry_label ? '<span class="project-similar__industry">' . $industry_label . '</span>' : '';
        $items .= '<a class="project-similar__item" href="' . esc_url(get_the_permalink()) . '">' . $thumb . $title . $industry . '</a>';
    }
    wp_reset_postdata();

    $grid_class = 'project-similar__grid' . ($similar->post_count >= 4 ? ' project-similar__grid--cols-4' : '');
    return '<div class="project-similar"><h2 class="has-heading-color has-text-color">Similar Projects</h2><div class="' . $grid_class . '">' . $items . '</div></div>';
}

/**
 * [year] shortcode — outputs the current four-digit year.
 * Usage in footer: © [year] Video Production Edinburgh
 */
add_shortcode('year', function () {
    return date('Y');
});
