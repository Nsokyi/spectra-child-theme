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
$service_link  = (!is_wp_error($services) && !empty($services)) ? get_term_link($services[0]) : $permalink;
$client_name   = function_exists('carbon_get_post_meta') ? carbon_get_post_meta($project_id, 'client_name') : get_post_meta($project_id, '_client_name', true);
$cta_label     = $service_label ? sprintf(__('Explore %s', 'spectra-child'), $service_label) : __('View case study', 'spectra-child');
?>

<div class="featured-project">
	<div class="featured-project__left"><span class="featured-project__label">// <?php esc_html_e('Featured Project', 'spectra-child'); ?></span><?php if ($thumbnail_url) : ?><a href="<?php echo esc_url($permalink); ?>" class="featured-project__media" aria-label="<?php echo esc_attr($title); ?>" tabindex="-1"><img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="eager" width="960" height="540"></a><?php else : ?><div class="featured-project__media featured-project__media--placeholder"><svg class="featured-project__play-icon" width="52" height="52" viewBox="0 0 52 52" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><circle cx="26" cy="26" r="25" stroke="currentColor" stroke-width="1.5"/><polygon points="22,18 36,26 22,34" fill="currentColor"/></svg></div><?php endif; ?></div>
	<div class="featured-project__content"><?php if ($client_name) : ?><span class="featured-project__kicker">// <?php echo esc_html($client_name); ?></span><?php endif; ?><h2 class="featured-project__title"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a></h2><?php if ($excerpt) : ?><p class="featured-project__excerpt"><?php echo esc_html($excerpt); ?></p><?php endif; ?><a href="<?php echo esc_url($service_link); ?>" class="vpe-text-link"><?php echo esc_html($cta_label); ?></a></div>
</div>
