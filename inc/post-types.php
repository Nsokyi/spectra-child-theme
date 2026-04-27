<?php
/**
 * Custom Post Types, Taxonomies & Term Population
 *
 * - Video Project CPT
 * - Testimonial CPT
 * - Service taxonomy
 * - Industry taxonomy
 * - Testimonial Group taxonomy
 * - Default term populators
 * - Flush rewrites on theme activation
 */

/**
 * Flush rewrite rules on theme activation (one-time)
 */
add_action('after_switch_theme', 'spectra_child_flush_rewrites');
function spectra_child_flush_rewrites() {
    spectra_child_create_video_project_cpt();
    spectra_child_create_service_taxonomy();
    spectra_child_create_industry_taxonomy();
    spectra_child_populate_service_terms();
    spectra_child_populate_industry_terms();
    flush_rewrite_rules();
}

/**
 * Register Custom Post Type: Video Projects
 */
add_action('init', 'spectra_child_create_video_project_cpt');
function spectra_child_create_video_project_cpt() {
    $labels = array(
        'name'                  => 'Video Projects',
        'singular_name'         => 'Video Project',
        'menu_name'             => 'Video Projects',
        'name_admin_bar'        => 'Video Project',
        'all_items'             => 'All Projects',
        'add_new_item'          => 'Add New Project',
        'add_new'               => 'Add New',
        'new_item'              => 'New Project',
        'edit_item'             => 'Edit Project',
        'update_item'           => 'Update Project',
        'view_item'             => 'View Project',
        'view_items'            => 'View Projects',
        'search_items'          => 'Search Projects',
    );
    
    $args = array(
        'label'                 => 'Video Project',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'excerpt', 'thumbnail'),
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-video-alt3',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'show_in_rest'          => true,
        'rewrite'               => array('slug' => 'projects'),
    );
    
    register_post_type('video-project', $args);
}

/**
 * Register Taxonomy: Services
 */
add_action('init', 'spectra_child_create_service_taxonomy');
function spectra_child_create_service_taxonomy() {
    $labels = array(
        'name'                       => 'Services',
        'singular_name'              => 'Service',
        'menu_name'                  => 'Services',
        'all_items'                  => 'All Services',
        'edit_item'                  => 'Edit Service',
        'view_item'                  => 'View Service',
        'update_item'                => 'Update Service',
        'add_new_item'               => 'Add New Service',
        'new_item_name'              => 'New Service Name',
        'search_items'               => 'Search Services',
    );
    
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'show_in_rest'               => true,
        'rewrite'                    => array('slug' => 'service'),
    );
    
    register_taxonomy('service', array('video-project'), $args);
}

/**
 * Register Taxonomy: Industries
 */
add_action('init', 'spectra_child_create_industry_taxonomy');
function spectra_child_create_industry_taxonomy() {
    $labels = array(
        'name'                       => 'Industries',
        'singular_name'              => 'Industry',
        'menu_name'                  => 'Industries',
        'all_items'                  => 'All Industries',
        'edit_item'                  => 'Edit Industry',
        'view_item'                  => 'View Industry',
        'update_item'                => 'Update Industry',
        'add_new_item'               => 'Add New Industry',
        'new_item_name'              => 'New Industry Name',
        'search_items'               => 'Search Industries',
    );
    
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'show_in_rest'               => true,
        'rewrite'                    => array('slug' => 'industry'),
    );
    
    register_taxonomy('industry', array('video-project'), $args);
}

/**
 * Auto-populate Service terms (called on theme activation)
 */
function spectra_child_populate_service_terms() {
    $services = array(
        'Video Production',
        'Promotional Videos',
        'Event Filming',
        'Event Production',
        'Music and Performance Videos',
        'Fringe Festival Filming',
        'Video Editing',
        'Videographer',
        'Educational Videos',
        'Conferences',
        'Motion Graphics',
        'Aerial and Drone Filming',
        'Live Streaming',
        'Hybrid Events',
        'Virtual Events',
        'Remote Filming',
        'Subtitling',
        'Consultancy',
    );
    
    foreach ($services as $service) {
        if (!term_exists($service, 'service')) {
            wp_insert_term($service, 'service');
        }
    }
}

/**
 * Auto-populate Industry terms (called on theme activation)
 */
function spectra_child_populate_industry_terms() {
    $industries = array(
        'Brand',
        'Product',
        'Public Sector & Charity',
        'Art & Fashion',
        'Social',
        'Corporate',
    );
    
    foreach ($industries as $industry) {
        if (!term_exists($industry, 'industry')) {
            wp_insert_term($industry, 'industry');
        }
    }
}

/**
 * Register Custom Post Type: Testimonials
 */
add_action('init', 'spectra_child_create_testimonial_cpt');
function spectra_child_create_testimonial_cpt() {
    $labels = array(
        'name'                  => 'Testimonials',
        'singular_name'         => 'Testimonial',
        'menu_name'             => 'Testimonials',
        'name_admin_bar'        => 'Testimonial',
        'all_items'             => 'All Testimonials',
        'add_new_item'          => 'Add New Testimonial',
        'add_new'               => 'Add New',
        'new_item'              => 'New Testimonial',
        'edit_item'             => 'Edit Testimonial',
        'update_item'           => 'Update Testimonial',
        'view_item'             => 'View Testimonial',
        'view_items'            => 'View Testimonials',
        'search_items'          => 'Search Testimonials',
    );

    $args = array(
        'label'                 => 'Testimonial',
        'labels'                => $labels,
        'supports'              => array('title'),
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 25,
        'menu_icon'             => 'dashicons-format-quote',
        'show_in_admin_bar'     => false,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'show_in_rest'          => true,
    );

    register_post_type('testimonial', $args);
}

/**
 * Register Taxonomy: Testimonial Groups
 */
add_action('init', 'spectra_child_create_testimonial_group_taxonomy');
function spectra_child_create_testimonial_group_taxonomy() {
    $labels = array(
        'name'                       => 'Groups',
        'singular_name'              => 'Group',
        'menu_name'                  => 'Groups',
        'all_items'                  => 'All Groups',
        'edit_item'                  => 'Edit Group',
        'view_item'                  => 'View Group',
        'update_item'                => 'Update Group',
        'add_new_item'               => 'Add New Group',
        'new_item_name'              => 'New Group Name',
        'search_items'               => 'Search Groups',
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => false,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => false,
        'show_tagcloud'              => false,
        'show_in_rest'               => true,
    );

    register_taxonomy('testimonial_group', array('testimonial'), $args);
}
