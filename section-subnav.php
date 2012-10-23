<?php

/**
 * Plugin Name: Section Subnav
 * Plugin URI: https://github.com/benknight/section-subnav
 * Description: A WordPress plugin that gives you the <code>section_subnav()</code> template tag to use anywhere in your theme to show a subnav menu.  Also makes a widget available to add to your theme's sidebars.
 * Version: 0.9
 * Author: Benjamin Knight
 * Author URI: http://benknight.me
 *
 * Section Subnav - Adds functionality for displaying only a piece of a site's navigation
 * as based on the current navigation state determined by WordPress-generated CSS hooks.
 * (current-menu-ancestor, current-menu-item, and current-menu-parent)
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package SectionSubnav
 * @version 0.9
 * @author Benjamin Knight <ben@benknight.me>
 * @copyright Copyright © 2011, Benjamin Knight
 * @link https://github.com/benknight/section-subnav
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
function section_subnav($args = array(), $instance = array())
{
    $which_menu = false;

    // Set up the default arguments for the breadcrumb.
    $defaults = array(
        'before_widget' => '<nav id="section-subnav" class="widget widget_section-subnav">',
        'after_widget' => "</nav>",
        'before_title' => '<h3 class="section-subnav-title widget-title">',
        'after_title' => '</h3>',
        'echo' => true
    );

    // Apply filters to the arguments.
    $args = apply_filters('section_subnav_args', $args);

    // Parse the arguments and extract them for easy variable naming.
    $args = wp_parse_args($args, $defaults);

    //we'll check if the user has decided to display a scpecific menu
    $which_menu = $instance['which_menu'] ? $instance['which_menu'] : false;

    //we'll check if the user has decided to display the section as title
    $section_title = $instance['title'] ? $instance['title'] : false;

    // Get all registered nav menus
    $nav_menus = get_registered_nav_menus();

    foreach (array_keys($nav_menus) as $nav_menu)
    {
        if($which_menu)
        {
            if($nav_menu != $which_menu)
            {
                continue;
            }
        }

        $nav = wp_nav_menu(array('theme_location' => $nav_menu, 'echo' => false));


        $xml = simplexml_load_string($nav);

        if (!empty($xml->ul))
        {
            foreach ($xml->ul[0]->li as $menu_item)
            {

                $menu_item_class = (string) $menu_item['class'];

                if (( strstr($menu_item_class, 'current-menu-ancestor')
                        || strstr($menu_item_class, 'current_page_ancestor')
                        || strstr($menu_item_class, 'current-menu-item')
                        || strstr($menu_item_class, 'current_page_item')
                        || strstr($menu_item_class, 'current-menu-parent')
                        || strstr($menu_item_class, 'current_page_parent') )
                        && !empty($menu_item->ul)
                        && ( strstr((string) $menu_item->ul[0]['class'], 'children') )
                        || strstr((string) $menu_item->ul[0]['class'], 'sub-menu'))
                {

                    $subnav = $args['before_widget'];

                    //the section title
                    if($section_title)
                        $subnav .= $args['before_title'] . $menu_item->a->asXML() . $args['after_title'];

                    //the main nav
                    $subnav .= $menu_item->ul->asXML();

                    $subnav .= $args['after_widget'];

                    if ($args['echo'])
                        echo $subnav;
                    else
                        return $subnav;
                }

            }
        }
    }

    return false;
}

class Section_Subnav extends WP_Widget {

    function Section_Subnav() {
        parent::WP_Widget('section-subnav', 'Section Subnav', array('description' => 'Show a sub-navigation menu based on registered theme menus.'));
    }

    function widget($args, $instance) {
        section_subnav($args, $instance);
    }

    function form($instance) {
        $instance = wp_parse_args((array) $instance, array('title' => ''));

        $which_menu = $instance['which_menu'];
        $title = $instance['title'];

        $nav_menus = get_registered_nav_menus();

        if (is_array($nav_menus) && !empty($nav_menus)):
            ?>
            <p>
                <label for="<?php echo $this->get_field_id('which_menu'); ?>">Which menu would you like to display?
                    <select id="<?php echo $this->get_field_id('which_menu'); ?>" name="<?php echo $this->get_field_name('which_menu'); ?>" value="<?php echo esc_attr($which_menu); ?>" >
            <?php foreach ($nav_menus as $key => $menu): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($which_menu == $key ? 'selected="selected"' : ''); ?>><?php echo $menu; ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>
        <?php endif; ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Display active section as title?
                <input type="checkbox" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" <?php echo (isset($title) ? 'checked="checked"' : ''); ?>/>
            </label>
        </p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['which_menu'] = $new_instance['which_menu'];
        return $instance;
    }

}

function sectionsubnav_register_widgets() {
    register_widget('Section_Subnav');
}

add_action('widgets_init', 'sectionsubnav_register_widgets');