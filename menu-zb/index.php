<?php
/*
Plugin Name: Menu Shortcodes by ZB
Description: Provides shortcodes for custom WordPress menus with custom structure and submenu depth classes.
Version: 1.0
Author: Zaman Bhuiyan
*/

// Renders the menu HTML with custom classes and submenu depth
function zb_render_custom_menu($items, $depth = 0) {
    if (empty($items)) return '';

    $is_submenu = $depth > 0;
    $ul_class = $is_submenu ? "submenu depth-$depth" : "menu depth-$depth";

    $output = "<ul class=\"$ul_class\">";

    foreach ($items as $item) {
        $has_children = !empty($item->children);
        $li_classes = ['menu-item'];
        if ($has_children) $li_classes[] = 'has-submenu';
        $output .= '<li class="' . implode(' ', $li_classes) . '">';
        $output .= '<a href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';

        if ($has_children) {
            $output .= zb_render_custom_menu($item->children, $depth + 1);
        }

        $output .= '</li>';
    }

    $output .= '</ul>';
    return $output;
}

// Get a hierarchical structure from a flat WP menu array
function zb_build_menu_tree($menu_items, $parent_id = 0) {
    $branch = [];

    foreach ($menu_items as $item) {
        if ((int)$item->menu_item_parent === (int)$parent_id) {
            $children = zb_build_menu_tree($menu_items, $item->ID);
            if (!empty($children)) {
                $item->children = $children;
            } else {
                $item->children = [];
            }
            $branch[] = $item;
        }
    }

    return $branch;
}

// Shortcode renderer
function zb_render_dynamic_menu_shortcode($atts = [], $content = null, $tag = '') {
    $menu_slug = str_replace('menu_', '', $tag);
    $menu_object = wp_get_nav_menu_object($menu_slug);
    if (!$menu_object) return '';

    $menu_items = wp_get_nav_menu_items($menu_object->term_id, ['orderby' => 'menu_order']);
    if (empty($menu_items)) return '';

    $tree = zb_build_menu_tree($menu_items);
    $html = '<div class="nav-container ' . esc_attr($menu_slug) . '">';
    $html .= '<h4 class="menu-title">' . esc_html($menu_object->name) . '</h4>';
    $html .= zb_render_custom_menu($tree);
    $html .= '</div>';

    return $html;
}

// Register shortcodes for each menu
function zb_register_dynamic_menu_shortcodes() {
    $menus = wp_get_nav_menus();
    foreach ($menus as $menu) {
        $slug = $menu->slug;
        $shortcode_tag = 'menu_' . $slug;
        add_shortcode($shortcode_tag, 'zb_render_dynamic_menu_shortcode');
    }
}
add_action('init', 'zb_register_dynamic_menu_shortcodes');

// Admin UI to list shortcodes
function zb_menu_shortcode_admin_page() {
    ?>
    <div class="wrap">
        <h1>Menu Shortcodes</h1>
        <p>Copy and paste the following shortcodes to render your menus:</p>
        <ul>
        <?php
        $menus = wp_get_nav_menus();
        foreach ($menus as $menu) {
            $shortcode = '[menu_' . esc_attr($menu->slug) . ']';
            echo '<li><code>' . esc_html($shortcode) . '</code> → ' . esc_html($menu->name) . '</li>';
        }
        ?>
        </ul>
    </div>
    <?php
}

function zb_menu_shortcode_admin_menu() {
    add_menu_page('Menu Shortcodes', 'Menu Shortcodes', 'manage_options', 'zb-menu-shortcodes', 'zb_menu_shortcode_admin_page');
}
add_action('admin_menu', 'zb_menu_shortcode_admin_menu');