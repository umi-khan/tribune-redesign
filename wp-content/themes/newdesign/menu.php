<?php

function register_menus()
{
	register_nav_menus(
		array(
			'main-menu' => 'Main Menu',
			'footer-menu' => 'Footer Menu',
			'top-brand-menu' => 'Top Brand Menu'
		)
	);
}
add_action( 'init', 'register_menus' );


function exp_nav_menu($args)
{
	if (array_key_exists('start_depth', $args)) {
		$args['depth'] = $args['depth'] ? $args['depth'] : 1;
		$args['depth'] = $args['depth'] +  $args['start_depth'];
	}
	$args['walker'] = new exp_nav_sub_menu_walker($args['depth']);
	wp_nav_menu($args);
}

add_filter('wp_get_nav_menu_items','exp_menu_items_add_classes');
add_filter('nav_menu_css_class','exp_menu_items_remove_classes');
add_filter('wp_nav_menu','exp_remove_empty_menu');

function exp_remove_empty_menu($menu_html)
{
	if( !strpos($menu_html,'</li>') ) $menu_html = '';
	return $menu_html;
}

function exp_menu_items_add_classes($items)
{
	$isChild = false;
	foreach($items as $key=>&$menu_item)
	{
		// resetting first and last classes
		array_unique($menu_item->classes);
		
		$class_first_key = array_search('first',$menu_item->classes);
		if($class_first_key !== false) unset($menu_item->classes[$class_first_key]);
		
		$class_last_key = array_search('last',$menu_item->classes);
		if($class_last_key !== false) unset($menu_item->classes[$class_last_key]);
		
		// adding first and lass classes
		if($key == 0 || ($menu_item->menu_item_parent && !$isChild) )
		{
			array_unshift($menu_item->classes, 'first');
		}
		
		if($menu_item->menu_item_parent)
		{
			$isChild = true;
		}
		else
		{
			if($isChild) array_unshift($items[$key-1]->classes, 'last');
			$isChild = false;
		}
	}
	array_unshift($items[count($items)-1]->classes, 'last');
	return $items;
}

function exp_menu_items_remove_classes($classes)
{
	$class_to_remove = array( 'menu-item', 'current-post-ancestor', 'current-post-parent', 'menu-item-type-taxonomy', 'current-category-ancestor', 'current-menu-ancestor', 'current-menu-parent', 'current-category-parent', 'menu-item-type-custom', 'menu-item-home', 'current-menu-item', 'current_page_item' );
	foreach($classes as $key=>$class)
	{
		if(in_array($class,$class_to_remove)) unset($classes[$key]);
	}
	return $classes;
}


class exp_nav_sub_menu_walker extends Walker_Nav_Menu {

	private static $current_item = array( 'parent' => false, 'child' => false );
	private $start_depth;
	private $has_added_current = array( false, false );
	
	function __construct($start_depth = 0)
	{
		$this->start_depth = $start_depth;
	}

	function start_el(&$output, $item, $depth, $args) {

		global $wp_query;
		
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';
		
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		
		if(   !$this->has_added_current[$depth] && 
				( in_array('current-menu-item',$classes) 
				|| in_array('current-menu-parent',$classes)
				|| in_array('current-post-ancestor',$classes)
				|| in_array('current-post-parent',$classes) ) )
					{
						array_unshift($classes, 'current');
						$this->has_added_current[$depth] = true;
					} 
		
		
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		
		$class_names = (trim($class_names)) ? ' class="' . esc_attr( $class_names ) . '"' : '' ;

		// Checks if the current element is in the current selection
		if ( strpos($class_names, 'current') )
		{
			if( !$item->menu_item_parent && self::$current_item['parent'] === false )
			{
				self::$current_item['parent'] = $item->ID;
			}
			else if(  self::$current_item['child'] === false && self::$current_item['parent'] == $item->menu_item_parent )
			{
				self::$current_item['child'] = $item->ID;
			}
		}
		if( $this->start_depth <= 1 ||
				( self::$current_item['parent'] !== false && $item->menu_item_parent == self::$current_item['parent'] ) )
		{

			$output .= $indent . '<li' . $class_names .'>';

			$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
			$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
			$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
			$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

			$item_output = $args->before;
			$item_output .= '<a'. $attributes .'>';
			$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
			$item_output .= '</a>';
			$item_output .= $args->after;

			$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}

	function end_el(&$output, $item, $depth) {
		// Closes only the opened li
		if( $this->start_depth <= 1 ||
				( self::$current_item['parent'] !== false && $item->menu_item_parent == self::$current_item['parent'] ) )
		{
			$output .= "</li>\n";
		}
	}

	function start_lvl(&$output, $depth) {
		return;
	}

	function end_lvl(&$output, $depth) {
		return;
	}

}