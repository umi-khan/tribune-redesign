<?php
/*
Plugin Name: Express Slideshows
Description: Adds support for creating slideshow and placing images into slideshows according to a subject of your choosing
 (for example, you can create a combination of "Favorite quotations", "Programming stuff", and "Day-to-day ramblings" slideshow).
Version: 1.0
Author: Express Media
Author URI: http://express.com.pk
Plugin URI: http://tribune.com.pk
*/

define('SLIDESHOWS_PLUGIN_DIR', trailingslashit(WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname(plugin_basename(__FILE__))));
define('SLIDESHOWS_PLUGIN_URL', trailingslashit(WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__))));

/*
 * including required files/classes for the plugin
 */
require_once SLIDESHOWS_PLUGIN_DIR . 'lib/slideshow.class.php';
require_once SLIDESHOWS_PLUGIN_DIR . 'lib/manager.class.php';
require_once SLIDESHOWS_PLUGIN_DIR . 'lib/widget.class.php';
require_once SLIDESHOWS_PLUGIN_DIR . 'lib/admin.class.php';

// initialize the plugin
$plugin_slideshows = new Plugin_slideshows();
$plugin_slideshows->init();

class Plugin_slideshows
{
	private $_post_type;
	private $_admin;

	public function  __construct()
	{		
		$this->_post_type = SS_manager::TYPE_NAME;
		if( is_admin() )	$this->_admin = new Slideshows_Admin();
	}

	public function init()
   {
   	$this->_hook_actions();
		$this->_hook_filters();
		$this->_register_widget();
	}

	public function post_type_link( $post_link, $post = null, $leavename = false )
	{
		if( $post->post_type == $this->_post_type )
			$post_link =  preg_replace( '@(http://.*\/)(.*)\/@', '${1}'.$post->ID, $post_link  );

		return $post_link;
	}

	public function register_post_type()
	{
		$labels = array(
		 'name'               => _x('Slideshows', 'post type general name'),
		 'singular_name'      => _x('Slideshow', 'post type singular name'),
		 'add_new'            => _x('Add New', $this->_post_type ),
		 'add_new_item'       => __('Add New Slideshow'),
		 'edit_item'          => __('Edit Slideshow'),
		 'new_item'           => __('New Slideshow'),
		 'view_item'          => __('View Slideshow'),
		 'search_items'       => __('Search Slideshows'),
		 'not_found'          =>  __('No slideshows found'),
		 'not_found_in_trash' => __('No slideshows found in Trash'),
		 'parent_item_colon'  => ''
	  );

		$args = array(
			'labels'          => $labels,
			'public'          => true,
			'show_ui'         => true,
			'menu_position'   => 5,
			'menu_icon'       => SLIDESHOWS_PLUGIN_URL . '/images/icon.ico',
			'query_var'       => true,
			'capability_type' => 'post',
			'supports'        => array( 'title', 'excerpt', 'comments' ),
			'rewrite'         => array( 'slug'=>"multimedia/slideshows", 'with_front' => FALSE ),
			'taxonomies'      => array( 'category')); // @@todo: allow tags

		register_post_type( $this->_post_type, $args);
	}

	public function post_updated_messages( $messages )
	{
		global $post_ID, $post;
		
		$messages[$this->_post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Slideshow updated. <a href="%s">View Slideshow</a>'), esc_url( get_permalink($post_ID) ) ),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __('Slideshow updated.'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Slideshow restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Slideshow published. <a href="%s">View Slideshow</a>'), esc_url( get_permalink($post_ID) ) ),
			7 => __('Slideshow saved.'),
			8 => sprintf( __('Slideshow submitted. <a target="_blank" href="%s">Preview Slideshow</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __('Slideshow scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Slideshow</a>'),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __('Slideshow draft updated. <a target="_blank" href="%s">Preview Slideshow</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) )
		);

		return $messages;
	}

	public function validate_slideshow()
	{				
		$slideshows_cats = get_option('post_category');
		
		if(! empty( $slideshows_cats ) && isset( $_POST['post_category'] ) && $_POST['post_status'] = 'publish' && $_POST['post_type'] == SS_manager::TYPE_NAME  )
		{
			$error_message = __('Atleast one category should be selected for this slideshow!');
			$selected_valid_categories = array_intersect( $slideshows_cats, $_POST['post_category'] );
			if( empty( $selected_valid_categories ) )
			{
				if( $_POST['action'] == 'inline-save' )
				{
					printf( $error_message ); exit();
				}
				wp_die( $error_message );
			}
		}
	}

	
	private function _hook_actions()
	{
		if( ! is_null( $this->_admin ) )	$this->_admin->hook_actions();

		add_action( 'save_post', array( $this, 'validate_slideshow' ) );
		add_action( 'init', array( $this, 'register_post_type' ) );
	}

	private function _hook_filters()
	{
		add_filter( 'post_type_link', array( $this, 'post_type_link' ) , 1, 3 );
		add_filter( 'post_updated_messages', array ( $this, 'post_updated_messages' ) );
	}

	private function _register_widget()
	{
		add_action( 'widgets_init', array('SS_widget', 'register' ) );
	}
}