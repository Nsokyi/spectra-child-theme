<?php
/**
 * Child Theme Functions
 */

// Include modular components.
require_once __DIR__ . '/inc/project-rest-api.php';
require_once __DIR__ . '/inc/project-block.php';
require_once __DIR__ . '/inc/project-enqueue.php';
require_once __DIR__ . '/inc/project-grid-shortcode.php';
require_once __DIR__ . '/inc/logo-carousel-block.php';

// Enqueue parent theme styles
function enqueue_parent_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'enqueue_parent_styles');

/**
 * Initialize Carbon Fields
 */
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('after_setup_theme', 'crb_load_carbon_fields');
function crb_load_carbon_fields() {
    require_once(__DIR__ . '/vendor/autoload.php');
    \Carbon_Fields\Carbon_Fields::boot();
}

/**
 * Flush rewrite rules on theme activation (one-time)
 */
add_action('after_switch_theme', 'spectra_child_flush_rewrites');
function spectra_child_flush_rewrites() {
    create_video_project_cpt();
    create_service_taxonomy();
    create_industry_taxonomy();
    populate_service_terms();
    populate_industry_terms();
    flush_rewrite_rules();
}

/**
 * Debug: Add body class to verify post type
 */
add_filter('body_class', 'video_project_body_class');
function video_project_body_class($classes) {
    if (is_singular('video-project')) {
        $classes[] = 'single-video-project';
        $classes[] = 'wp-embed-responsive';
    }
    return $classes;
}

/**
 * Ensure block theme compatibility for video-project CPT
 */
add_action('wp_enqueue_scripts', 'video_project_enqueue_block_styles', 20);
function video_project_enqueue_block_styles() {
    if (is_singular('video-project')) {
        // Ensure global styles are loaded
        wp_enqueue_style('global-styles');

        $theme_uri = get_stylesheet_directory_uri();
        $css_path  = get_stylesheet_directory() . '/assets/css/project-testimonial.css';

        if (file_exists($css_path)) {
            wp_enqueue_style(
                'project-testimonial',
                $theme_uri . '/assets/css/project-testimonial.css',
                array(),
                filemtime($css_path)
            );
        }
    }
}

/**
 * Register Custom Post Type: Video Projects
 */
add_action('init', 'create_video_project_cpt');
function create_video_project_cpt() {
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
add_action('init', 'create_service_taxonomy');
function create_service_taxonomy() {
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
add_action('init', 'create_industry_taxonomy');
function create_industry_taxonomy() {
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
function populate_service_terms() {
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
function populate_industry_terms() {
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
 * Register Custom Fields with Carbon Fields
 */
add_action('carbon_fields_register_fields', 'register_video_project_fields');
function register_video_project_fields() {
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
 * Helper: Resolve post ID from shortcode attributes
 */
function resolve_video_project_id($atts) {
    $atts = is_array($atts) ? $atts : array();
    $atts = shortcode_atts(array('id' => ''), $atts);
    $post_id = !empty($atts['id']) ? intval($atts['id']) : get_the_ID();
    
    if (!$post_id || get_post_type($post_id) !== 'video-project') {
        return false;
    }
    
    return $post_id;
}

/**
 * Shortcode: Display Video Project Meta (Client, Agency, Featured Badge)
 */
add_shortcode('video_project_meta', 'render_video_project_meta');
function render_video_project_meta($atts) {
    $post_id = resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }
    
    $client_name = carbon_get_post_meta($post_id, 'client_name');
    $agency = carbon_get_post_meta($post_id, 'agency');
    $is_featured = carbon_get_post_meta($post_id, 'featured_project');
    
    ob_start();
    ?>
    <div class="video-project-meta">
        <?php if ($is_featured) : ?>
            <div class="featured-badge has-primary-background-color has-background">
                ⭐ Featured Project
            </div>
        <?php endif; ?>
        
        <?php if ($client_name || $agency) : ?>
            <div class="project-info">
                <?php if ($client_name) : ?>
                    <div class="client-info">
                        <strong class="has-neutral-color has-text-color">Client</strong>
                        <span class="has-heading-color has-text-color"><?php echo esc_html($client_name); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($agency) : ?>
                    <div class="agency-info">
                        <strong class="has-neutral-color has-text-color">Agency</strong>
                        <span class="has-heading-color has-text-color"><?php echo esc_html($agency); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Display Video Embed
 */
add_shortcode('video_project_embed', 'render_video_project_embed');
function render_video_project_embed($atts) {
    $post_id = resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }
    
    $video_url = carbon_get_post_meta($post_id, 'video_url');
    $video_duration = carbon_get_post_meta($post_id, 'video_duration');
    
    if (!$video_url) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="video-project-embed">
        <div class="video-container">
            <iframe 
                src="<?php echo esc_url($video_url); ?>" 
                frameborder="0" 
                allow="autoplay; fullscreen; picture-in-picture" 
                allowfullscreen>
            </iframe>
        </div>
        <?php if ($video_duration) : ?>
            <p class="video-duration has-neutral-color has-text-color">
                Duration: <?php echo esc_html($video_duration); ?>
            </p>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Display Services and Industries Taxonomies
 */
add_shortcode('video_project_taxonomies', 'render_video_project_taxonomies');
function render_video_project_taxonomies($atts) {
    $post_id = resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }
    
    $services = get_the_terms($post_id, 'service');
    $industries = get_the_terms($post_id, 'industry');
    
    if (!$services && !$industries) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="video-project-taxonomies wp-block-group has-surface-background-color has-background">
        <div class="taxonomy-grid">
            <?php if ($services) : ?>
                <div class="services-list">
                    <h3 class="has-heading-color has-text-color">Services</h3>
                    <ul>
                        <?php foreach ($services as $service) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_term_link($service)); ?>" 
                                   class="taxonomy-pill has-background-color has-background has-body-color has-text-color">
                                    <?php echo esc_html($service->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if ($industries) : ?>
                <div class="industries-list">
                    <h3 class="has-heading-color has-text-color">Industries</h3>
                    <ul>
                        <?php foreach ($industries as $industry) : ?>
                            <li>
                                <a href="<?php echo esc_url(get_term_link($industry)); ?>" 
                                   class="taxonomy-pill has-background-color has-background has-body-color has-text-color">
                                    <?php echo esc_html($industry->name); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Display Production Credits
 */
add_shortcode('video_project_credits', 'render_video_project_credits');
function render_video_project_credits($atts) {
    $post_id = resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }
    
    $credits = carbon_get_post_meta($post_id, 'credits');
    
    if (!$credits || empty($credits)) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="video-project-credits wp-block-group">
        <h3 class="has-heading-color has-text-color">Production Credits</h3>
        <div class="credits-grid">
            <?php foreach ($credits as $credit) : ?>
                <?php
                $role = isset($credit['role']) ? $credit['role'] : '';
                $name = isset($credit['name']) ? $credit['name'] : '';
                if (empty($role) && empty($name)) continue;
                ?>
                <div class="credit-item has-surface-background-color has-background">
                    <strong class="has-neutral-color has-text-color">
                        <?php echo esc_html($role); ?>
                    </strong>
                    <span class="has-heading-color has-text-color">
                        <?php echo esc_html($name); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Shortcode: Display Client Testimonial
 *
 * Requires both testimonial_rating and testimonial_text to render.
 * Author name and photo are optional.
 */
add_shortcode('video_project_testimonial', 'render_video_project_testimonial');
function render_video_project_testimonial($atts) {
    $post_id = resolve_video_project_id($atts);
    if (!$post_id) {
        return '';
    }

    $rating = carbon_get_post_meta($post_id, 'testimonial_rating');
    $text   = carbon_get_post_meta($post_id, 'testimonial_text');

    if (!$rating || !$text) {
        return '';
    }

    $rating = intval($rating);
    $author = carbon_get_post_meta($post_id, 'testimonial_author');
    $photo  = carbon_get_post_meta($post_id, 'testimonial_photo');

    $star_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
    $empty_star_svg = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';

    ob_start();
    ?>
    <div class="project-testimonial wp-block-group">
        <div class="project-testimonial__stars" role="img" aria-label="<?php echo esc_attr(sprintf(__('%d out of 5 stars', 'spectra-child'), $rating)); ?>">
            <?php for ($i = 1; $i <= 5; $i++) : ?>
                <span class="project-testimonial__star<?php echo $i <= $rating ? ' is-filled' : ''; ?>">
                    <?php echo $i <= $rating ? $star_svg : $empty_star_svg; ?>
                </span>
            <?php endfor; ?>
        </div>

        <blockquote class="project-testimonial__quote">
            <p class="has-body-color has-text-color"><?php echo esc_html($text); ?></p>
        </blockquote>

        <?php if ($author || $photo) : ?>
            <div class="project-testimonial__author">
                <?php if ($photo) : ?>
                    <img class="project-testimonial__photo"
                         src="<?php echo esc_url($photo); ?>"
                         alt="<?php echo esc_attr($author ?: __('Client photo', 'spectra-child')); ?>"
                         width="48"
                         height="48"
                         loading="lazy">
                <?php endif; ?>
                <?php if ($author) : ?>
                    <span class="project-testimonial__name has-heading-color has-text-color"><?php echo esc_html($author); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}