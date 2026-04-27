<?php
/**
 * Carbon Fields — Bootstrap & Custom Field Registrations
 *
 * - Loads Carbon Fields via Composer autoload
 * - Registers Video Project meta fields (tabbed)
 * - Registers Testimonial CPT meta fields
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('after_setup_theme', 'crb_load_carbon_fields');
function crb_load_carbon_fields() {
    require_once(get_stylesheet_directory() . '/vendor/autoload.php');
    \Carbon_Fields\Carbon_Fields::boot();
}

/**
 * Register Custom Fields with Carbon Fields
 */
add_action('carbon_fields_register_fields', 'spectra_child_register_video_project_fields');
add_action('carbon_fields_register_fields', 'spectra_child_register_testimonial_fields');
function spectra_child_register_video_project_fields() {
    Container::make('post_meta', __('Video Project Details'))
        ->where('post_type', '=', 'video-project')
        
        // === VIDEO TAB ===
        ->add_tab(__('Video'), array(
            Field::make('text', 'video_url', __('Video Embed URL'))
                ->set_attribute('type', 'url')
                ->set_attribute('placeholder', 'https://player.vimeo.com/video/123456789')
                ->set_help_text(__('Use the embed URL format: https://player.vimeo.com/video/ID or https://www.youtube.com/embed/ID'))
                ->set_required(true),
            
            Field::make('text', 'video_duration', __('Duration'))
                ->set_attribute('placeholder', '2:30')
                ->set_help_text(__('Format: MM:SS or H:MM:SS'))
                ->set_width(30),
        ))
        
        // === PROJECT INFO TAB ===
        ->add_tab(__('Project Info'), array(
            Field::make('checkbox', 'featured_project', __('Featured Project'))
                ->set_option_value('yes')
                ->set_help_text(__('Display this project prominently on the homepage/archive')),
            
            Field::make('text', 'client_name', __('Client'))
                ->set_attribute('placeholder', 'e.g., Mercedes-Benz Vans')
                ->set_required(true)
                ->set_width(50),
            
            Field::make('text', 'agency', __('Agency'))
                ->set_attribute('placeholder', 'e.g., Goldbug Creative')
                ->set_width(50),
        ))
        
        // === CREDITS TAB ===
        ->add_tab(__('Production Credits'), array(
            Field::make('complex', 'credits', __('Crew & Credits'))
                ->set_help_text(__('Add team members who worked on this project'))
                ->add_fields(array(
                    Field::make('text', 'role', __('Role'))
                        ->set_attribute('placeholder', 'e.g., Director')
                        ->set_width(40),
                    Field::make('text', 'name', __('Name'))
                        ->set_attribute('placeholder', 'e.g., John Smith')
                        ->set_width(60),
                ))
                ->set_header_template('<%- role %>: <%- name %>')
                ->set_layout('tabbed-horizontal')
                ->set_collapsed(true),
        ))
        
        // === PRODUCTION STILLS TAB ===
        ->add_tab(__('Production Stills'), array(
            Field::make('media_gallery', 'production_stills', __('Stills Gallery'))
                ->set_type(array('image'))
                ->set_help_text(__('Upload still images from the video project. These will display in a grid on the front-end.')),
        ))
        
        // === CLIENT TESTIMONIAL TAB ===
        ->add_tab(__('Client Testimonial'), array(
            Field::make('select', 'testimonial_rating', __('Star Rating'))
                ->set_options(array(
                    ''  => __('— Select rating —'),
                    '1' => '★ (1 star)',
                    '2' => '★★ (2 stars)',
                    '3' => '★★★ (3 stars)',
                    '4' => '★★★★ (4 stars)',
                    '5' => '★★★★★ (5 stars)',
                ))
                ->set_help_text(__('Required for testimonial to display'))
                ->set_width(30),
            
            Field::make('textarea', 'testimonial_text', __('Testimonial'))
                ->set_attribute('placeholder', __('What the client said about the project…'))
                ->set_help_text(__('Required for testimonial to display'))
                ->set_rows(4),
            
            Field::make('text', 'testimonial_author', __('Author Name'))
                ->set_attribute('placeholder', __('e.g., Jane Smith, Marketing Director'))
                ->set_help_text(__('Optional: Name and title of the person giving the testimonial'))
                ->set_width(50),
            
            Field::make('image', 'testimonial_photo', __('Author Photo'))
                ->set_help_text(__('Optional: Small portrait of the testimonial author'))
                ->set_value_type('url')
                ->set_width(50),
        ));
}

/**
 * Register Custom Fields for Testimonials CPT
 */
function spectra_child_register_testimonial_fields() {
    Container::make('post_meta', __('Testimonial Details'))
        ->where('post_type', '=', 'testimonial')
        ->add_fields(array(
            Field::make('textarea', 'testimonial_quote', __('Client Quote'))
                ->set_attribute('placeholder', __('What the client said…'))
                ->set_required(true)
                ->set_rows(4),

            Field::make('select', 'testimonial_rating', __('Star Rating'))
                ->set_options(array(
                    ''  => __('— Select rating —'),
                    '1' => '★ (1 star)',
                    '2' => '★★ (2 stars)',
                    '3' => '★★★ (3 stars)',
                    '4' => '★★★★ (4 stars)',
                    '5' => '★★★★★ (5 stars)',
                ))
                ->set_required(true)
                ->set_width(30),

            Field::make('text', 'testimonial_client_name', __('Client Name'))
                ->set_attribute('placeholder', __('e.g., Jane Smith'))
                ->set_required(true)
                ->set_width(50),

            Field::make('text', 'testimonial_job_title', __('Job Title'))
                ->set_attribute('placeholder', __('e.g., Marketing Director'))
                ->set_width(50),

            Field::make('text', 'testimonial_company', __('Company Name'))
                ->set_attribute('placeholder', __('e.g., Mercedes-Benz'))
                ->set_width(50),

            Field::make('image', 'testimonial_client_photo', __('Client Photo'))
                ->set_help_text(__('Small portrait of the testimonial author'))
                ->set_value_type('url')
                ->set_width(50),
        ));
}
