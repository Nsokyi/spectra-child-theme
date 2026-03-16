<?php
/**
 * Block Render: Testimonials Slider
 *
 * Queries all published testimonial CPT posts and renders them in a slider.
 * Testimonial data is stored via Carbon Fields on the testimonial post type.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

$auto_slide = !empty($attributes['autoSlide']);
$auto_speed = !empty($attributes['autoSlideSpeed']) ? max(2, min(15, intval($attributes['autoSlideSpeed']))) : 5;
$group_id   = !empty($attributes['testimonialGroup']) ? intval($attributes['testimonialGroup']) : 0;

$query_args = array(
    'post_type'      => 'testimonial',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
);

if ($group_id) {
    $query_args['tax_query'] = array(
        array(
            'taxonomy' => 'testimonial_group',
            'field'    => 'term_id',
            'terms'    => $group_id,
        ),
    );
}

$testimonial_query = new WP_Query($query_args);

if (!$testimonial_query->have_posts()) {
    wp_reset_postdata();
    return;
}

$star_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
$empty_star_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
$quote_svg = '<svg width="21" height="16" viewBox="0 0 21 16" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M-2.6226e-06 15.5455V11.4546C-2.6226e-06 10.2121 0.219694 8.89395 0.659088 7.50002C1.11363 6.09092 1.76515 4.73486 2.61363 3.43184C3.47727 2.11365 4.51515 0.969711 5.72727 1.52588e-05L8.63636 2.36365C7.68182 3.72729 6.84848 5.15153 6.13636 6.63638C5.43939 8.10608 5.09091 9.68183 5.09091 11.3637V15.5455H-2.6226e-06ZM11.6364 15.5455V11.4546C11.6364 10.2121 11.8561 8.89395 12.2955 7.50002C12.75 6.09092 13.4015 4.73486 14.25 3.43184C15.1136 2.11365 16.1515 0.969711 17.3636 1.52588e-05L20.2727 2.36365C19.3182 3.72729 18.4848 5.15153 17.7727 6.63638C17.0758 8.10608 16.7273 9.68183 16.7273 11.3637V15.5455H11.6364Z" fill="#9498A0"/></svg>';

$card_count = 0;

$wrapper_attributes = get_block_wrapper_attributes(array(
    'class'      => 'wp-block-spectra-child-testimonials-slider',
    'role'       => 'region',
    'aria-label' => __('Client Testimonials', 'spectra-child'),
    'data-auto-slide' => $auto_slide ? 'true' : 'false',
    'data-auto-speed' => esc_attr($auto_speed),
));
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="testimonials-slider__track">
        <?php while ($testimonial_query->have_posts()) : $testimonial_query->the_post();
            $post_id  = get_the_ID();
            $quote    = carbon_get_post_meta($post_id, 'testimonial_quote');
            $stars    = carbon_get_post_meta($post_id, 'testimonial_rating');
            $name     = carbon_get_post_meta($post_id, 'testimonial_client_name');
            $job      = carbon_get_post_meta($post_id, 'testimonial_job_title');
            $company  = carbon_get_post_meta($post_id, 'testimonial_company');
            $photo    = carbon_get_post_meta($post_id, 'testimonial_client_photo');

            if (!$quote) continue;
            if (!$name) {
                $name = __('Anonymous', 'spectra-child');
            }

            $stars = $stars ? max(1, min(5, intval($stars))) : 5;
            $subtitle_parts = array_filter(array($job, $company));
            $subtitle = implode(', ', $subtitle_parts);
            $card_count++;
        ?>
            <div class="testimonials-slider__card" aria-label="<?php echo esc_attr(sprintf(__('Testimonial from %s', 'spectra-child'), $name)); ?>">
                <div class="testimonials-slider__content">
                    <div class="testimonials-slider__quote-icon" aria-hidden="true">
                        <?php echo $quote_svg; ?>
                    </div>

                    <blockquote class="testimonials-slider__quote">
                        <p><?php echo esc_html($quote); ?></p>
                    </blockquote>

                    <div class="testimonials-slider__stars" role="img" aria-label="<?php echo esc_attr(sprintf(__('%d out of 5 stars', 'spectra-child'), $stars)); ?>">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <span class="testimonials-slider__star<?php echo $i <= $stars ? ' is-filled' : ''; ?>">
                                <?php echo $i <= $stars ? $star_svg : $empty_star_svg; ?>
                            </span>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="testimonials-slider__author">
                    <?php if ($photo) : ?>
                        <img class="testimonials-slider__photo"
                             src="<?php echo esc_url($photo); ?>"
                             alt="<?php echo esc_attr($name); ?>"
                             width="63"
                             height="63"
                             loading="lazy">
                    <?php endif; ?>

                    <div class="testimonials-slider__author-info">
                        <?php if ($name) : ?>
                            <span class="testimonials-slider__name"><?php echo esc_html($name); ?></span>
                        <?php endif; ?>
                        <?php if ($subtitle) : ?>
                            <span class="testimonials-slider__role"><?php echo esc_html($subtitle); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>

    <?php if ($card_count > 1) : ?>
        <div class="testimonials-slider__nav">
            <button class="testimonials-slider__arrow testimonials-slider__arrow--prev" aria-label="<?php esc_attr_e('Previous testimonial', 'spectra-child'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
            </button>
            <button class="testimonials-slider__arrow testimonials-slider__arrow--next" aria-label="<?php esc_attr_e('Next testimonial', 'spectra-child'); ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
            </button>
        </div>
    <?php endif; ?>
</div>
