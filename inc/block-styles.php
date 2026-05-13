<?php
/**
 * Block Styles: Register custom Gutenberg block style variations.
 *
 * Adds "Kicker" and "Kicker Small" styles to Heading and Paragraph blocks.
 * These appear in the editor's Styles panel when a block is selected.
 */
add_action('init', 'spectra_child_register_block_styles');
function spectra_child_register_block_styles() {
    $kicker_styles = array(
        array(
            'name'  => 'kicker',
            'label' => __('Kicker', 'spectra-child'),
        ),
        array(
            'name'  => 'kicker-small',
            'label' => __('Kicker Small', 'spectra-child'),
        ),
    );

    $blocks = array('core/heading', 'core/paragraph');

    foreach ($blocks as $block) {
        foreach ($kicker_styles as $style) {
            register_block_style($block, $style);
        }
    }
}
