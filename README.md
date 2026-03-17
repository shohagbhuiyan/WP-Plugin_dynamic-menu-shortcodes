# WP Menu Shortcodes by ZB

A lightweight WordPress plugin that automatically creates a shortcode for each registered navigation menu and renders it using a custom hierarchical HTML structure.

## Features

- Automatically registers one shortcode per WordPress menu
- Uses menu slug as the shortcode name
- Outputs nested menu HTML with submenu depth classes
- Adds a WordPress admin page listing all available shortcodes
- Useful for custom theme development, template layouts, footers, sidebars, and content blocks

## How it works

If you have a WordPress menu with the slug:

`footer-menu`

the plugin automatically creates this shortcode:

`[menu_footer-menu]`

When used in a post, page, widget, or template, it renders that menu in a custom structure.

## Example output

```html
<div class="nav-container footer-menu">
  <h4 class="menu-title">Footer Menu</h4>
  <ul class="menu depth-0">
    <li class="menu-item">
      <a href="/about">About</a>
    </li>
    <li class="menu-item has-submenu">
      <a href="/services">Services</a>
      <ul class="submenu depth-1">
        <li class="menu-item">
          <a href="/services/design">Design</a>
        </li>
      </ul>
    </li>
  </ul>
</div>
