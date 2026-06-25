<?php
/**
 * Legacy URL Redirects
 *
 * Handles 301 redirects for taxonomy rewrite slug changes.
 * Add new entries here whenever a public URL pattern changes.
 */

/**
 * Redirect old /service/{slug}/ URLs to /portfolio/{slug}/
 *
 * The service taxonomy rewrite slug was changed from 'service' to 'portfolio'.
 * This redirect preserves any bookmarked or externally linked URLs.
 */
add_action('template_redirect', 'spectra_child_legacy_taxonomy_redirects');
function spectra_child_legacy_taxonomy_redirects() {
    $request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
    
    if ( preg_match( '#^/service/([^/]+)/?$#', $request_uri, $matches ) ) {
        wp_redirect( home_url( '/portfolio/' . sanitize_title($matches[1]) . '/' ), 301 );
        exit;
    }
}






