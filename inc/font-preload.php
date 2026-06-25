<?php
/**
 * Preload the primary body font (Roboto 400) to remove the FCP-blocking
 * discovery delay.
 *
 * The Roboto 400 .woff2 file is installed via the WP Font Library
 * (wp_font_face post ID 389) and served from /wp-content/uploads/fonts/.
 * The hashed filename comes from Google Fonts' own filename scheme and
 * is stable as long as the font isn't reinstalled.
 *
 * Roboto Mono 400 is not preloaded — it's only used for kickers and
 * captions, which are not in the LCP element on any page.
 */
add_action( 'wp_head', 'spectra_child_preload_primary_font', 1 );
function spectra_child_preload_primary_font() {
	$href = content_url( '/uploads/fonts/KFOMCnqEu92Fr1ME7kSn66aGLdTylUAMQXC89YmC2DPNWubEbWmWggvWl0Qn.woff2' );
	printf(
		'<link rel="preload" as="font" type="font/woff2" href="%s" crossorigin="anonymous">' . "\n",
		esc_url( $href )
	);
}
