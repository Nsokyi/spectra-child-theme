<?php
/**
 * Custom Nav Walker: VPE Desktop Navigation
 *
 * Outputs dropdown items with title + description spans.
 * Expects menu items to use the built-in "Description" field
 * (enable via Screen Options in Appearance → Menus).
 *
 * Parent items with children get a <button> trigger instead of <a>.
 */
class VPE_Nav_Walker extends Walker_Nav_Menu {

    /**
     * Opens the sub-menu wrapper.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '<div class="dropdown-panel" role="menu">';
        $output .= '<ul class="dropdown-list">';
    }

    /**
     * Closes the sub-menu wrapper.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= '</ul></div>';
    }

    /**
     * Renders each menu item.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $has_children = in_array( 'menu-item-has-children', $classes, true );

        // Top-level parent → becomes dropdown trigger
        if ( $depth === 0 && $has_children ) {
            $output .= '<li class="has-dropdown">';
            $output .= '<button class="dropdown-trigger" aria-expanded="false" aria-haspopup="true">';
            $output .= esc_html( $item->title );
            $output .= '<svg class="chevron" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            $output .= '</button>';
            return;
        }

        // Top-level regular item
        if ( $depth === 0 ) {
            $active = in_array( 'current-menu-item', $classes, true ) ? ' current-menu-item' : '';
            $output .= '<li class="' . esc_attr( trim( $active ) ) . '">';
            $output .= '<a href="' . esc_url( $item->url ) . '">';
            $output .= esc_html( $item->title );
            $output .= '</a>';
            return;
        }

        // Sub-menu item (depth 1) → dropdown card with title + description
        $output .= '<li role="none">';
        $output .= '<a href="' . esc_url( $item->url ) . '" role="menuitem" class="dropdown-item">';
        $output .= '<span class="dropdown-item__title">' . esc_html( $item->title ) . '</span>';
        if ( ! empty( $item->description ) ) {
            $output .= '<span class="dropdown-item__desc">' . esc_html( $item->description ) . '</span>';
        }
        $output .= '</a>';
    }

    /**
     * Closes each menu item.
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= '</li>';
    }
}
