<?php
/**
 * Custom Nav Walker: VPE Mobile Navigation
 *
 * Outputs mobile drawer menu with accordion sub-menus.
 * Parent items with children get a <button> trigger with chevron
 * and a "View all" link inside the sub-menu for accessibility.
 */
class VPE_Mobile_Nav_Walker extends Walker_Nav_Menu {

    private $parent_url   = '';
    private $parent_title = '';

    /**
     * Opens the sub-menu wrapper.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<ul class="mobile-sub-menu" aria-hidden="true">';
        // Add "View all" link at the top of sub-menu so parent page remains accessible
        if ( $depth === 0 && $this->parent_url ) {
            $lowercase_title = function_exists( 'mb_strtolower' ) ? mb_strtolower( $this->parent_title ) : strtolower( $this->parent_title );
            $label = sprintf( __( 'Explore %s', 'spectra-child' ), $lowercase_title );
            $output .= '<li><a href="' . esc_url( $this->parent_url ) . '" class="mobile-sub-view-all">' . esc_html( $label ) . '</a></li>';
            $this->parent_url   = '';
            $this->parent_title = '';
        }
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
        $classes = apply_filters( 'nav_menu_css_class', $classes, $item, $args, $depth );
        $has_children = in_array( 'menu-item-has-children', $classes, true );

        // Top-level parent → accordion trigger, stash URL for "View all" link
        if ( $depth === 0 && $has_children ) {
            $this->parent_url   = $item->url;
            $this->parent_title = $item->title;
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
