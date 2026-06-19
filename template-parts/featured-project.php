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
$excerpt       = get_the_excerpt($project_id);
$services      = get_the_terms($project_id, 'service');
$service_label = (!is_wp_error($services) && !empty($services)) ? $services[0]->name : '';
$client_name   = function_exists('carbon_get_post_meta') ? carbon_get_post_meta($project_id, 'client_name') : get_post_meta($project_id, '_client_name', true);
$cta_label = __('View project', 'spectra-child');
?>

<div class="featured-project">
	<div class="featured-project__left"><span class="featured-project__label">// <?php esc_html_e('Featured Project', 'spectra-child'); ?></span><?php $thumbnail_id = get_post_thumbnail_id($project_id); if ($thumbnail_id) : ?><a href="<?php echo esc_url($permalink); ?>" class="featured-project__media" aria-label="<?php echo esc_attr($title); ?>" tabindex="-1"><?php echo wp_get_attachment_image($thumbnail_id, 'large', false, ['loading' => 'eager']); ?></a><?php else : ?><div class="featured-project__media featured-project__media--placeholder"><svg class="featured-project__play-icon" width="52" height="52" viewBox="0 0 52 52" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"><circle cx="26" cy="26" r="25" stroke="currentColor" stroke-width="1.5"/><polygon points="22,18 36,26 22,34" fill="currentColor"/></svg></div><?php endif; ?></div>
	<div class="featured-project__content"><?php if ($client_name) : ?><span class="featured-project__kicker">// <?php echo esc_html($client_name); ?></span><?php endif; ?><h2 class="featured-project__title"><a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a></h2><?php if ($excerpt) : ?><p class="featured-project__excerpt"><?php echo esc_html($excerpt); ?></p><?php endif; ?><a href="<?php echo esc_url($permalink); ?>" class="vpe-text-link"><?php echo esc_html($cta_label); ?></a></div>
</div>
