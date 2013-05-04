# Section Subnav
Contributors: benknight
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=NYCGJ7YCGUTAQ&lc=US&item_name=Benjamin%20Knight&item_number=section%2dsubnav&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: widget, menu, navigation
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: 0.9

Adds a widget and template function for displaying subnavigation based on the current navigation state.

## Description

This is a simple plugin that was born out of working on several projects with sectional navigations.  For example, consider the following menu as set from Appearance > Menus in the WordPress admin:

`
* Home
* Item
* About Us
 * Sub-item
 * Sub-item
* Item
* Item
`

Then, whenever on the "About Us" page or any of its sub-items, it would output that peice of the navigation:

`
* About Us
 * Sub-item
 * Sub-item
`

This is particularly useful for websites that have a top horizontal navigation which shows top-level items and want to show a vertical subnavigation in the sidebar.

This plugin also exposes the `section_subnav()` function for theme developers to use as a template tag to manually place a subnav inside the theme:

`<?php
	
	section_subnav( array(
		'before_widget' => '<nav id="section-subnav" class="widget widget_section-subnav">',
		'after_widget' => "</nav>",
		'before_title' => '<h3 class="section-subnav-title widget-title">',
		'after_title' => '</h3>',
		'echo' => true
	));

?>`

It returns false when there is no subnavigation to show.

This function also provides the `section_subnav_args` filter hook for writing less code and easier integration with other plugins and child themes.

## Installation

This section describes how to install the plugin and get it working.

1. Upload `section-subnav` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add the widget to a sidebar or use the `section_subnav()` function in one of your theme templates.

## Frequently Asked Questions

### How does this plugin work?

This plugin works by parsing the output of the `wp_nav_menu` function as XML and analyzing the CSS class hooks (current-menu-ancestor, current-menu-item, and current-menu-parent).  Because it uses PHP's SimpleXML library it therefore requires PHP 5+.  It uses the theme's registered menu locations.

### I added the widget or `section_subnav()` function but there is no output even when there is subavigation to show.

Like WordPress, this plugin is only aware of the menus you "tell" it about, meaning only those that are created in the Menus screen and assigned to one of your theme's locations.

## Screenshots

## Changelog 

### 0.9.1
* Added the ability to specify a custom widget title.

### 0.9
* Initial release.
