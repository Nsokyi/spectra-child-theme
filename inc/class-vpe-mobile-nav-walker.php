<?php
/**
 * Custom Nav Walker: VPE Mobile Navigation
 *
 * Outputs mobile drawer menu with accordion sub-menus.
 * Parent items with children get a <button> trigger with chevron.
 */
class VPE_Mobile_Nav_Walker extends Walker_Nav_Menu {

    /**
     * Opens the sub-menu wrapper.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<ul class="mobile-sub-menu" aria-hidden="true">';
    }

    /**
     * Closes the sub-menu wrapper.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</ul>';
    }

    /**
     * Renders each menu item.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $has_children = in_array( 'menu-item-has-children', $classes, true );

        // Top-level parent → accordion trigger
        if ( $depth === 0 && $has_children ) {
            $output .= '<li class="mobile-has-sub">';
            $output .= '<button class="mobile-sub-trigger" aria-expanded="false">';
            $output .= esc_html( $item->title );
            $output .= '<svg class="chevron" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            $output .= '</button>';
            return;
        }

        // Regular item
        $output .= '<li>';
        $output .= '<a href="' . esc_url( $item->url ) . '">';
        $output .= esc_html( $item->title );
        $output .= '</a>';
    }

    /**
     * Closes each menu item.
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</li>';
    }
}
