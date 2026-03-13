<?php
/**
 * Block Render: Testimonials Slider
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

$testimonials   = !empty($attributes['testimonials']) ? $attributes['testimonials'] : array();
$auto_slide     = !empty($attributes['autoSlide']);
$auto_speed     = !empty($attributes['autoSlideSpeed']) ? max(2, min(15, intval($attributes['autoSlideSpeed']))) : 5;

if (empty($testimonials)) {
    return;
}

$star_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
$empty_star_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
$quote_svg = '<svg width="21" height="16" viewBox="0 0 21 16" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><path d="M-2.6226e-06 15.5455V11.4546C-2.6226e-06 10.2121 0.219694 8.89395 0.659088 7.50002C1.11363 6.09092 1.76515 4.73486 2.61363 3.43184C3.47727 2.11365 4.51515 0.969711 5.72727 1.52588e-05L8.63636 2.36365C7.68182 3.72729 6.84848 5.15153 6.13636 6.63638C5.43939 8.10608 5.09091 9.68183 5.09091 11.3637V15.5455H-2.6226e-06ZM11.6364 15.5455V11.4546C11.6364 10.2121 11.8561 8.89395 12.2955 7.50002C12.75 6.09092 13.4015 4.73486 14.25 3.43184C15.1136 2.11365 16.1515 0.969711 17.3636 1.52588e-05L20.2727 2.36365C19.3182 3.72729 18.4848 5.15153 17.7727 6.63638C17.0758 8.10608 16.7273 9.68183 16.7273 11.3637V15.5455H11.6364Z" fill="#9498A0"/></svg>';

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
        <?php foreach ($testimonials as $index => $testimonial) :
            $quote    = !empty($testimonial['quote']) ? $testimonial['quote'] : '';
            $stars    = isset($testimonial['stars']) ? max(1, min(5, intval($testimonial['stars']))) : 5;
            $name     = !empty($testimonial['name']) ? $testimonial['name'] : '';
            $position = !empty($testimonial['position']) ? $testimonial['position'] : '';
            $company  = !empty($testimonial['company']) ? $testimonial['company'] : '';
            $photo    = !empty($testimonial['photoUrl']) ? $testimonial['photoUrl'] : '';

            if (!$quote) continue;

            $subtitle_parts = array_filter(array($position, $company));
            $subtitle = implode(', ', $subtitle_parts);
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
        <?php endforeach; ?>
    </div>

    <?php if (count($testimonials) > 1) : ?>
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
