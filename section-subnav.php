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

function section_subnav( $args = array(), $instance = array() ) {

	// Set up the default arguments for the breadcrumb.
	$defaults = array(
		'before_widget' => '<nav id="section-subnav" class="widget widget_section-subnav">',
		'after_widget' => "</nav>",
		'before_title' => '<h3 class="section-subnav-title widget-title">',
		'after_title' => '</h3>',
		'echo' => true
	);

	// Apply filters to the arguments.
	$args = apply_filters( 'section_subnav_args', $args );

	// Parse the arguments and extract them for easy variable naming.
	$args = wp_parse_args( $args, $defaults );
	
	// Get all registered nav menus
	$nav_menus = get_registered_nav_menus();
				
	foreach ( array_keys( $nav_menus ) as $nav_menu ) {
	
		$nav = wp_nav_menu( array( 'theme_location' => $nav_menu, 'echo' => false ) );
		
		$xml = simplexml_load_string( $nav );
		
		if ( ! empty( $xml->ul ) ) : foreach ( $xml->ul[0]->li as $menu_item ) :
		
			$menu_item_class = (string) $menu_item['class'];
			
			if ( ( strstr( $menu_item_class, 'current-menu-ancestor' )
				|| strstr( $menu_item_class, 'current-menu-item' )
				|| strstr( $menu_item_class, 'current-menu-parent' ) )
				&& ! empty( $menu_item->ul ) 
				&& strstr( (string) $menu_item->ul[0]['class'], 'sub-menu' ) ) {
				
					$instance_title = empty( $instance['title'] ) ? $menu_item->a->asXML() : $instance['title'];
					$subnav  = $args['before_widget'];
					$subnav .= $args['before_title'] . $instance_title . $args['after_title'];
					$subnav .= $menu_item->ul->asXML();
					$subnav .= $args['after_widget'];
	
					if ( $args['echo'] )
						echo $subnav;
					else
						return $subnav;
			}
		
		endforeach; endif;
	}
	
	return false;
}

class Section_Subnav extends WP_Widget {

	function Section_Subnav() {
		parent::WP_Widget( 'section-subnav', 'Section Subnav', array( 'description' => 'Show a sub-navigation menu based on registered theme menus.' ) );
	}

	function widget( $args, $instance ) {
		section_subnav( $args, $instance );
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}
	
	function form( $instance ) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>">
		</p>
		<?php 
	}

}

function sectionsubnav_register_widgets() {
	register_widget( 'Section_Subnav' );
}
add_action( 'widgets_init', 'sectionsubnav_register_widgets' );
