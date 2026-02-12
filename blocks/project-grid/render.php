<?php
/**
 * Block Render: Project Grid
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

$featured_only = !empty($attributes['showFeaturedOnly']);
$per_page      = !empty($attributes['postsPerPage']) ? intval($attributes['postsPerPage']) : 12;

$wrapper_attributes = get_block_wrapper_attributes(array(
    'class' => 'wp-block-spectra-child-project-grid',
));
?>

<div <?php echo $wrapper_attributes; ?>>
    <?php
    get_template_part('template-parts/project-grid', null, array(
        'featured_only' => $featured_only,
        'per_page'      => $per_page,
    ));
    ?>
</div>
