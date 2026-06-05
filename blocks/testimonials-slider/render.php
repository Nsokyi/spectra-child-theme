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

$auto_slide    = !empty($attributes['autoSlide']);
$auto_speed    = !empty($attributes['autoSlideSpeed']) ? max(2, min(15, intval($attributes['autoSlideSpeed']))) : 5;
$group_id      = !empty($attributes['testimonialGroup']) ? intval($attributes['testimonialGroup']) : 0;
$order_ids     = !empty($attributes['testimonialOrder']) ? array_map('intval', $attributes['testimonialOrder']) : array();

$tax_query = array();
if ($group_id) {
    $tax_query = array(
        array(
            'taxonomy' => 'testimonial_group',
            'field'    => 'term_id',
            'terms'    => $group_id,
        ),
    );
}

$ordered_posts   = array();
$remainder_posts = array();

if (!empty($order_ids)) {
    $ordered_query = new WP_Query(array(
        'post_type'      => 'testimonial',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__in'       => $order_ids,
        'orderby'        => 'post__in',
        'tax_query'      => $tax_query ?: array(),
    ));
    $ordered_posts = $ordered_query->posts;
    wp_reset_postdata();

    $remainder_args = array(
        'post_type'      => 'testimonial',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__not_in'   => $order_ids,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => $tax_query ?: array(),
    );
    $remainder_query = new WP_Query($remainder_args);
    $remainder_posts = $remainder_query->posts;
    wp_reset_postdata();
} else {
    $default_query = new WP_Query(array(
        'post_type'      => 'testimonial',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => $tax_query ?: array(),
    ));
    $remainder_posts = $default_query->posts;
    wp_reset_postdata();
}

$all_posts = array_merge($ordered_posts, $remainder_posts);

if (empty($all_posts)) {
    return;
}

$star_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
$empty_star_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
$quote_svg = '<svg width="18" height="13" viewBox="0 0 18 13" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path fill="#aaa" d="M3.58403 12.6722c-.98133 0-1.83467-.3413-2.56-1.024-.682666-.7253-1.024-1.6213-1.024-2.68801 0-2.00533.576001-3.776 1.728-5.312 1.19467-1.57866 2.56-2.77333 4.096-3.583996l.256-.064q.127995 0 .192.127999c.08533.085332.128.170666.128.256 0 .128001-.04267.234668-.128.32-1.57867 1.407997-2.368 2.709337-2.368 3.903997 0 .68267.29867 1.152.896 1.408.68267.34134 1.216.74667 1.6 1.216.42667.46934.64 1.10934.64 1.92 0 .98131-.34133 1.81331-1.024 2.49601-.64.6827-1.45067 1.024-2.432 1.024m10.62397 0c-.9813 0-1.8346-.3413-2.56-1.024-.6826-.7253-1.024-1.6213-1.024-2.68801 0-2.00533.576-3.776 1.728-5.312 1.1947-1.57866 2.56-2.77333 4.096-3.583996l.256-.064q.1281 0 .192.127999c.0854.085332.128.170666.128.256 0 .128001-.0426.234668-.128.32-1.5786 1.407997-2.368 2.709337-2.368 3.903997 0 .68267.2987 1.152.896 1.408.6827.34134 1.216.74667 1.6 1.216.4267.46934.64 1.10934.64 1.92 0 .98131-.3413 1.81331-1.024 2.49601-.64.6827-1.4506 1.024-2.432 1.024"/></svg>';

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
        <?php foreach ($all_posts as $post) :
            $post_id  = $post->ID;
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
            $subtitle = implode(', ', $subtitle_parts); // kept for aria-label fallback
            $card_count++;
        ?>
            <div class="testimonials-slider__card" aria-label="<?php echo esc_attr(sprintf(__('Testimonial from %s', 'spectra-child'), $name)); ?>">
                <div class="testimonials-slider__content">
                    <div class="testimonials-slider__quote-icon" aria-hidden="true">
                        <?php echo $quote_svg; ?>
                    </div>

                    <div class="testimonials-slider__quote-wrap">
                        <blockquote class="testimonials-slider__quote" id="testimonial-quote-<?php echo esc_attr($post_id); ?>">
                            <p><?php echo esc_html($quote); ?></p>
                        </blockquote>
                        <div class="testimonials-slider__fade" aria-hidden="true"></div>
                    </div>

                    <button class="testimonials-slider__toggle"
                            type="button"
                            aria-expanded="false"
                            aria-controls="testimonial-quote-<?php echo esc_attr($post_id); ?>">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                        <span><?php esc_html_e('Read more', 'spectra-child'); ?></span>
                    </button>

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
                        <?php if ($job) : ?>
                            <span class="testimonials-slider__role"><?php echo esc_html($job); ?></span>
                        <?php endif; ?>
                        <?php if ($company) : ?>
                            <span class="testimonials-slider__company"><?php echo esc_html($company); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
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
