<?php
/**
 * Block Render: Project Grid
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

$featured_only = !empty($attributes['showFeaturedOnly']);
$per_page      = !empty($attributes['postsPerPage']) ? max(1, min(100, intval($attributes['postsPerPage']))) : 6;
$show_load_more  = isset($attributes['showLoadMore']) ? (bool) $attributes['showLoadMore'] : true;
$show_filter_bar = isset($attributes['showFilterBar']) ? (bool) $attributes['showFilterBar'] : true;
$columns         = !empty($attributes['columns']) ? intval($attributes['columns']) : 3;
$orderby         = !empty($attributes['orderBy']) && $attributes['orderBy'] === 'menu_order' ? 'menu_order' : 'date';

$current_term = null;
$pre_industry = !empty($attributes['preSelectedIndustry']) ? sanitize_title($attributes['preSelectedIndustry']) : '';
$pre_service  = !empty($attributes['preSelectedService']) ? sanitize_title($attributes['preSelectedService']) : '';

if ($pre_industry) {
    $current_term = array(
        'taxonomy' => 'industry',
        'slug'     => $pre_industry,
    );
} elseif ($pre_service) {
    $current_term = array(
        'taxonomy' => 'service',
        'slug'     => $pre_service,
    );
}

$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => 'wp-block-spectra-child-project-grid',
));
?>

<div <?php echo $wrapper_attributes; ?>>
    <?php
    get_template_part('template-parts/project-grid', null, array(
        'featured_only'   => $featured_only,
        'per_page'        => $per_page,
        'current_term'    => $current_term,
        'show_filter_bar' => $show_filter_bar,
        'show_load_more'  => $show_load_more,
        'columns'         => $columns,
        'orderby'         => $orderby,
    ));
    ?>
</div>
