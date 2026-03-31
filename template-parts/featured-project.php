<?php
/**
 * Template Part: Featured Project Hero
 *
 * Accepts $args:
 *   - project_id (int) The post ID of the featured project to display.
 */

$project_id = !empty($args['project_id']) ? intval($args['project_id']) : 0;
if (!$project_id) return;

$title         = get_the_title($project_id);
$permalink     = get_permalink($project_id);
$thumbnail_url = get_the_post_thumbnail_url($project_id, 'large');
$excerpt       = get_the_excerpt($project_id);
$services      = get_the_terms($project_id, 'service');
$service_label = (!is_wp_error($services) && !empty($services)) ? $services[0]->name : '';
?>

<div class="featured-project">
	<?php if ($thumbnail_url) : ?><a href="<?php echo esc_url($permalink); ?>" class="featured-project__media" aria-label="<?php echo esc_attr($title); ?>" tabindex="-1"><img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="eager" width="960" height="540"></a><?php else : ?><div class="featured-project__media featured-project__media--placeholder"></div><?php endif; ?>

	<div class="featured-project__content">
		<div class="featured-project__meta">
			<span class="featured-project__label"><?php esc_html_e('Featured Project', 'spectra-child'); ?></span>
			<?php if ($service_label) : ?>
				<span class="featured-project__service"><?php echo esc_html($service_label); ?></span>
			<?php endif; ?>
		</div>
		<h2 class="featured-project__title">
			<a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
		</h2>
		<?php if ($excerpt) : ?>
			<p class="featured-project__excerpt"><?php echo esc_html($excerpt); ?></p>
		<?php endif; ?>
		<a href="<?php echo esc_url($permalink); ?>" class="featured-project__link">
			<?php esc_html_e('View case study', 'spectra-child'); ?>
			<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
				<path d="M3 8H13M13 8L9 4M13 8L9 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
		</a>
	</div>
</div>
