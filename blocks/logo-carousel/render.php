<?php
/**
 * Block Render: Logo Carousel
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

$logos       = !empty($attributes['logos']) ? $attributes['logos'] : array();
$speed       = !empty($attributes['speed']) ? max(5, min(120, intval($attributes['speed']))) : 30;
$logo_height = !empty($attributes['logoHeight']) ? max(20, min(200, intval($attributes['logoHeight']))) : 50;

if (empty($logos)) {
    return;
}

// Repeat logos enough times so the track is always wider than the viewport.
// We need at least 2 full copies; with few logos we need more.
$logo_count = count($logos);
$repeats    = max(2, ceil(12 / $logo_count)) * 2;

$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => 'wp-block-spectra-child-logo-carousel',
    'role'  => 'region',
    'aria-label' => __('Client Logos', 'spectra-child'),
    'style' => '--carousel-speed: ' . esc_attr($speed) . 's; --logo-height: ' . esc_attr($logo_height) . 'px; --carousel-items: ' . esc_attr($repeats / 2) . ';',
));
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="logo-carousel__track">
        <?php for ($r = 0; $r < $repeats; $r++) : ?>
            <div class="logo-carousel__set" <?php echo $r >= ($repeats / 2) ? 'aria-hidden="true"' : ''; ?>>
                <?php foreach ($logos as $logo) :
                    if (empty($logo['url'])) continue;
                    $alt  = !empty($logo['alt']) ? $logo['alt'] : '';
                    $link = !empty($logo['link']) ? $logo['link'] : '';
                ?>
                    <?php if ($link) : ?>
                        <a href="<?php echo esc_url($link); ?>" class="logo-carousel__item" target="_blank" rel="noopener noreferrer">
                            <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
                        </a>
                    <?php else : ?>
                        <span class="logo-carousel__item">
                            <img src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>
