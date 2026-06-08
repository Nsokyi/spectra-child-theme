<?php
/**
 * VPE Breadcrumb Navigation
 *
 * Provides a semantic breadcrumb trail and auto-injects it after the
 * header template part via the render_block filter.
 *
 * Excluded on: front page and all top-level pages (pages with no parent).
 * Shown on: child pages, blog posts, archives, search results, and 404s.
 */

if ( ! function_exists( 'vpe_breadcrumb' ) ) :

/**
 * Output or return a breadcrumb trail.
 *
 * @param  bool   $return Whether to return the HTML instead of echoing it.
 * @return string|void
 */
function vpe_breadcrumb( $return = false ) {

    // Never show on the front page.
    if ( is_front_page() ) {
        return $return ? '' : null;
    }

    $items    = array();
    $home_url = home_url( '/' );

    // Home is always first.
    $items[] = '<li class="vpe-breadcrumb__item"><a href="' . esc_url( $home_url ) . '">' . esc_html__( 'Home', 'spectra-child' ) . '</a></li>';

    // --- Single Video Project ---
    if ( is_singular( 'video-project' ) ) {
        $archive_url = get_post_type_archive_link( 'video-project' );
        $items[]     = '<li class="vpe-breadcrumb__item"><a href="' . esc_url( $archive_url ) . '">' . esc_html__( 'Projects', 'spectra-child' ) . '</a></li>';
        $items[]     = '<li class="vpe-breadcrumb__item vpe-breadcrumb__current"><span aria-current="page">' . esc_html( get_the_title() ) . '</span></li>';

    // --- Single Post (blog) ---
    } elseif ( is_singular( 'post' ) ) {
        $blog_url = get_permalink( get_option( 'page_for_posts' ) );
        if ( $blog_url ) {
            $items[] = '<li class="vpe-breadcrumb__item"><a href="' . esc_url( $blog_url ) . '">' . esc_html__( 'Blog', 'spectra-child' ) . '</a></li>';
        }
        $items[] = '<li class="vpe-breadcrumb__item vpe-breadcrumb__current"><span aria-current="page">' . esc_html( get_the_title() ) . '</span></li>';

    // --- Standard Page ---
    } elseif ( is_page() ) {
        // Build ancestor chain.
        $ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
        foreach ( $ancestors as $ancestor_id ) {
            $items[] = '<li class="vpe-breadcrumb__item"><a href="' . esc_url( get_permalink( $ancestor_id ) ) . '">' . esc_html( get_the_title( $ancestor_id ) ) . '</a></li>';
        }
        $items[] = '<li class="vpe-breadcrumb__item vpe-breadcrumb__current"><span aria-current="page">' . esc_html( get_the_title() ) . '</span></li>';

    // --- Video Project Archive ---
    } elseif ( is_post_type_archive( 'video-project' ) ) {
        $items[] = '<li class="vpe-breadcrumb__item vpe-breadcrumb__current"><span aria-current="page">' . esc_html__( 'Projects', 'spectra-child' ) . '</span></li>';

    // --- Service / Industry Taxonomy ---
    } elseif ( is_tax( 'service' ) || is_tax( 'industry' ) ) {
        $archive_url = get_post_type_archive_link( 'video-project' );
        $items[]     = '<li class="vpe-breadcrumb__item"><a href="' . esc_url( $archive_url ) . '">' . esc_html__( 'Projects', 'spectra-child' ) . '</a></li>';
        $items[]     = '<li class="vpe-breadcrumb__item vpe-breadcrumb__current"><span aria-current="page">' . esc_html( single_term_title( '', false ) ) . '</span></li>';

    // --- Category / Tag Archives ---
    } elseif ( is_category() || is_tag() ) {
        $blog_url = get_permalink( get_option( 'page_for_posts' ) );
        if ( $blog_url ) {
            $items[] = '<li class="vpe-breadcrumb__item"><a href="' . esc_url( $blog_url ) . '">' . esc_html__( 'Blog', 'spectra-child' ) . '</a></li>';
        }
        $items[] = '<li class="vpe-breadcrumb__item vpe-breadcrumb__current"><span aria-current="page">' . esc_html( single_term_title( '', false ) ) . '</span></li>';

    // --- Blog / Posts Archive ---
    } elseif ( is_home() ) {
        $items[] = '<li class="vpe-breadcrumb__item vpe-breadcrumb__current"><span aria-current="page">' . esc_html__( 'Blog', 'spectra-child' ) . '</span></li>';

    // --- General Archive (date, author) ---
    } elseif ( is_archive() ) {
        $items[] = '<li class="vpe-breadcrumb__item vpe-breadcrumb__current"><span aria-current="page">' . esc_html( get_the_archive_title() ) . '</span></li>';

    // --- Search ---
    } elseif ( is_search() ) {
        /* translators: %s: search query */
        $label   = sprintf( esc_html__( 'Search results for "%s"', 'spectra-child' ), get_search_query() );
        $items[] = '<li class="vpe-breadcrumb__item vpe-breadcrumb__current"><span aria-current="page">' . $label . '</span></li>';

    // --- 404 ---
    } elseif ( is_404() ) {
        $items[] = '<li class="vpe-breadcrumb__item vpe-breadcrumb__current"><span aria-current="page">' . esc_html__( 'Page not found', 'spectra-child' ) . '</span></li>';
    }

    if ( count( $items ) < 2 ) {
        return $return ? '' : null;
    }

    $html  = '<nav class="vpe-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'spectra-child' ) . '">';
    $html .= '<ol class="vpe-breadcrumb__list">' . implode( '', $items ) . '</ol>';
    $html .= '</nav>';

    if ( $return ) {
        return $html;
    }

    echo $html;
}

endif;

/**
 * Register [vpe_breadcrumb] shortcode for manual placement.
 */
add_shortcode( 'vpe_breadcrumb', function () {
    return vpe_breadcrumb( true );
} );

/**
 * Add body class to top-level pages for styling hooks.
 */
add_filter( 'body_class', 'vpe_add_top_level_body_class' );
function vpe_add_top_level_body_class( $classes ) {
    if ( is_page() && ! is_front_page() ) {
        global $post;
        if ( isset( $post->post_parent ) && $post->post_parent == 0 ) {
            $classes[] = 'is-top-level-page';
        }
    }
    return $classes;
}

/**
 * Auto-inject breadcrumbs after the header template part.
 *
 * Excluded on: front page and main pages (about, services, projects, contact).
 */
add_filter( 'render_block', 'vpe_inject_breadcrumb_after_header', 10, 2 );

function vpe_inject_breadcrumb_after_header( $block_content, $block ) {

    // Target either the header template part or the custom site-header block.
    $is_header_part  = $block['blockName'] === 'core/template-part'
                       && isset( $block['attrs']['slug'] )
                       && $block['attrs']['slug'] === 'header';
    $is_header_block = $block['blockName'] === 'spectra-child/site-header';

    if ( ! $is_header_part && ! $is_header_block ) {
        return $block_content;
    }

    // Skip front page.
    if ( is_front_page() ) {
        return $block_content;
    }

    // Skip breadcrumbs on top-level pages (no parent) to keep main marketing pages clean.
    // Child pages, posts, and archives will still show breadcrumbs.
    global $post;
    if ( is_page() && isset( $post->post_parent ) && $post->post_parent == 0 ) {
        return $block_content;
    }

    $breadcrumb = vpe_breadcrumb( true );

    if ( $breadcrumb ) {
        $block_content .= $breadcrumb;
    }

    return $block_content;
}
