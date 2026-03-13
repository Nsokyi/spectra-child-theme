<?php
/**
 * Register the Project Grid Gutenberg block.
 */

add_action('init', 'spectra_child_register_project_grid_block');

function spectra_child_register_project_grid_block() {
    register_block_type(get_stylesheet_directory() . '/blocks/project-grid');
}
