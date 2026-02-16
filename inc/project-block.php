<?php
/**
 * Register the Project Grid Gutenberg block.
 */

add_action('init', 'register_project_grid_block');

function register_project_grid_block() {
    register_block_type(get_stylesheet_directory() . '/blocks/project-grid');
}
