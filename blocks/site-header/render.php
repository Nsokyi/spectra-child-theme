<?php
/**
 * Block Render: Site Header
 *
 * Outputs the full site header with logo, desktop nav (wp_nav_menu),
 * mobile drawer, CTA button, and overlay.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block content.
 * @var WP_Block $block      Block instance.
 */

$cta_text = ! empty( $attributes['ctaText'] ) ? $attributes['ctaText'] : __( 'Get in touch', 'spectra-child' );
$cta_url  = ! empty( $attributes['ctaUrl'] )  ? $attributes['ctaUrl']  : '/contact/';

$home_url = home_url( '/' );

// Use the WordPress custom logo if set (Appearance → Customize → Site Identity),
// otherwise fall back to theme asset.
$custom_logo_id = get_theme_mod( 'custom_logo' );
if ( $custom_logo_id ) {
    $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
} else {
    $logo_url = get_stylesheet_directory_uri() . '/assets/images/vpe__logo-00.svg';
}

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'wp-block-spectra-child-site-header',
) );
?>

<div <?php echo $wrapper_attributes; ?>>
<header class="site-header" id="site-header">
    <nav class="site-nav" id="site-nav">
        <div class="site-nav__inner">

            <a href="<?php echo esc_url( $home_url ); ?>" class="site-nav__logo" aria-label="<?php esc_attr_e( 'Home', 'spectra-child' ); ?>"><img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="logo-img" width="120" height="36" /></a>

            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'site-nav__menu',
                'menu_id'        => 'primary-menu',
                'items_wrap'     => '<ul id="%1$s" class="%2$s" role="list">%3$s</ul>',
                'depth'          => 2,
                'walker'         => new VPE_Nav_Walker(),
                'fallback_cb'    => false,
            ) );
            ?>

            <div class="site-nav__actions">
                <a href="<?php echo esc_url( $cta_url ); ?>" class="site-nav__cta"><?php echo esc_html( $cta_text ); ?></a>
                <button class="site-nav__hamburger" id="hamburger-btn" aria-label="<?php esc_attr_e( 'Open menu', 'spectra-child' ); ?>" aria-expanded="false" aria-controls="mobile-drawer"><span class="bar bar--top"></span><span class="bar bar--mid"></span><span class="bar bar--bot"></span></button>
            </div>

        </div>
    </nav>

    <div class="mobile-drawer" id="mobile-drawer" aria-hidden="true">
        <div class="mobile-drawer__inner">
            <button class="mobile-drawer__close" id="drawer-close-btn" aria-label="<?php esc_attr_e( 'Close menu', 'spectra-child' ); ?>"><svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M2 2l14 14M16 2L2 16" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/></svg></button>

            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'mobile-nav__menu',
                'menu_id'        => 'mobile-menu',
                'items_wrap'     => '<ul id="%1$s" class="%2$s" role="list">%3$s</ul>',
                'depth'          => 2,
                'walker'         => new VPE_Mobile_Nav_Walker(),
                'fallback_cb'    => false,
            ) );
            ?>

            <a href="<?php echo esc_url( $cta_url ); ?>" class="mobile-nav__cta"><?php echo esc_html( $cta_text ); ?></a>
        </div>
    </div>

    <div class="mobile-overlay" id="mobile-overlay" aria-hidden="true"></div>
</header>
</div>
