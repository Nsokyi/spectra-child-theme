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
    $autoload = get_stylesheet_directory() . '/vendor/autoload.php';
    if ( ! file_exists( $autoload ) ) {
        return;
    }
    require_once $autoload;
    \Carbon_Fields\Carbon_Fields::boot();
}

/**
 * Register Custom Fields with Carbon Fields
 */
add_action('carbon_fields_register_fields', 'spectra_child_register_video_project_fields');
add_action('carbon_fields_register_fields', 'spectra_child_register_testimonial_fields');
add_action('carbon_fields_register_fields', 'spectra_child_register_project_options_page');
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
        ))
        
        // === PROJECT CONTENT TAB ===
        ->add_tab(__('Project Content'), array(
            Field::make('rich_text', 'project_brief', __('The Brief'))
                ->set_help_text(__('Describe the client\'s challenge and objectives. Supports paragraphs and bullet lists.'))
                ->set_required(true),
            
            Field::make('rich_text', 'project_approach', __('The Approach'))
                ->set_help_text(__('Describe the creative strategy and production process. Supports paragraphs and bullet lists.')),
            
            Field::make('rich_text', 'project_outcome', __('The Outcome'))
                ->set_help_text(__('Describe the results and impact. This renders in a dark highlight card on the front-end.')),
        ))
        
        // === PROJECT DETAILS TAB ===
        ->add_tab(__('Project Details'), array(
            Field::make('checkbox', 'featured_project', __('Featured Project'))
                ->set_option_value('yes')
                ->set_help_text(__('Display this project prominently on the homepage/archive')),
            
            Field::make('text', 'client_name', __('Client'))
                ->set_attribute('placeholder', 'e.g., University of Edinburgh')
                ->set_required(true)
                ->set_width(50),
            
            Field::make('text', 'project_duration', __('Project Duration'))
                ->set_attribute('placeholder', 'e.g., 7 weeks')
                ->set_help_text(__('How long the project took from start to delivery'))
                ->set_width(50),
            
            Field::make('text', 'delivered_date', __('Delivered'))
                ->set_attribute('placeholder', 'e.g., September 2025')
                ->set_help_text(__('When the project was completed'))
                ->set_width(50),
        ))
        
        // === SIDEBAR CTA TAB ===
        ->add_tab(__('Sidebar CTA'), array(
            Field::make('html', 'sidebar_cta_info')
                ->set_html(__('<p style="color:#666;">Leave fields empty to use the global defaults from <strong>VPE Project Settings</strong>.</p>')),
            
            Field::make('text', 'sidebar_cta_heading', __('CTA Heading'))
                ->set_attribute('placeholder', 'e.g., Planning a similar project?')
                ->set_width(100),
            
            Field::make('textarea', 'sidebar_cta_text', __('CTA Supporting Text'))
                ->set_rows(3)
                ->set_width(100),
            
            Field::make('text', 'sidebar_cta_button_label', __('Button Label'))
                ->set_attribute('placeholder', 'e.g., Start your project')
                ->set_width(50),
            
            Field::make('text', 'sidebar_cta_button_url', __('Button URL'))
                ->set_attribute('type', 'url')
                ->set_attribute('placeholder', '/contact-us/')
                ->set_width(50),
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
                ->set_attribute('placeholder', __('e.g., Sarah McAllister'))
                ->set_width(50),
            
            Field::make('text', 'testimonial_author_role', __('Author Role / Organisation'))
                ->set_attribute('placeholder', __('e.g., Head of Communications, NHS Scotland'))
                ->set_width(50),
            
            Field::make('image', 'testimonial_photo', __('Author Photo'))
                ->set_help_text(__('Optional: Small portrait of the testimonial author'))
                ->set_value_type('url')
                ->set_width(50),
        ));
}

/**
 * Theme Options: Global CTA Defaults for Project Sidebar
 */
function spectra_child_register_project_options_page() {
    Container::make('theme_options', __('VPE Project Settings'))
        ->set_page_parent('edit.php?post_type=video-project')
        ->add_fields(array(
            Field::make('text', 'default_cta_heading', __('Default CTA Heading'))
                ->set_default_value(__('Planning a similar project?'))
                ->set_width(100),
            
            Field::make('textarea', 'default_cta_text', __('Default CTA Supporting Text'))
                ->set_default_value(__('We work with universities, schools, and public sector organisations across Scotland. Tell us what you\'re trying to achieve.'))
                ->set_rows(3)
                ->set_width(100),
            
            Field::make('text', 'default_cta_button_label', __('Default Button Label'))
                ->set_default_value(__('Start your project'))
                ->set_width(50),
            
            Field::make('text', 'default_cta_button_url', __('Default Button URL'))
                ->set_default_value('/contact-us/')
                ->set_attribute('type', 'url')
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
