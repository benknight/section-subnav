# Section Subnav

Adds a widget and template function for displaying subnavigation based on the current navigation state.

***Note:* I no longer bother keeping the WordPress Plugin SVN repository up to date.  This Github repository is the most up-to-date version of this plugin.**

## Description

This is a simple plugin that was born out of working on several projects with sectional navigations.  For example, consider the following menu as set from Appearance > Menus in the WordPress admin:

* Home
* Item
* About Us
 * Sub-item
 * Sub-item
* Item
* Item

Then, whenever on the "About Us" page or any of its sub-items, it would output that peice of the navigation:

* About Us
 * Sub-item
 * Sub-item

This is particularly useful for websites that have a top horizontal navigation which shows top-level items and want to show a vertical subnavigation in the sidebar.

This plugin also exposes the `section_subnav()` function for theme developers to use as a template tag to manually place a subnav inside the theme:

```php
section_subnav( array(
	'before_widget' => '<nav id="section-subnav" class="widget widget_section-subnav">',
	'after_widget' => "</nav>",
	'before_title' => '<h3 class="section-subnav-title widget-title">',
	'after_title' => '</h3>',
	'echo' => true
));
```

It returns false when there is no subnavigation to show.

This function also provides the `section_subnav_args` filter hook for writing less code and easier integration with other plugins and child themes.

## Installation

This section describes how to install the plugin and get it working.

1. Upload `section-subnav` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the widget to a sidebar or use the `section_subnav()` function in one of your theme templates.
