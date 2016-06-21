<?php

/*
 * Plugin Name: Express Stories Management
 * Description: A plugin that lets users add authors and location to stories. This plugin also manages story excerpts
 * Author: Express Media
 * Author URI: http://express.com.pk/
 * Plugin URI: http://tribune.com.pk/
 * Version: 2.0
 */

define( 'SM_PLUGIN_DIR', trailingslashit( WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) ) );
define( 'SM_PLUGIN_URL', trailingslashit( WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) ) );

/*
 * including required files/classes for the plugin
 */
require_once SM_PLUGIN_DIR . 'lib/author.class.php';
require_once SM_PLUGIN_DIR . 'lib/author_manager.class.php';
require_once SM_PLUGIN_DIR . 'lib/author_admin.class.php';
require_once SM_PLUGIN_DIR . 'lib/location_manager.class.php';
require_once SM_PLUGIN_DIR . 'lib/location_admin.class.php';
require_once SM_PLUGIN_DIR . 'lib/content_admin.class.php';
require_once SM_PLUGIN_DIR . 'lib/magazineFeatured_admin.class.php';
require_once SM_PLUGIN_DIR . 'lib/special_featured_admin.class.php';


// set up the plugin activation hook
register_activation_hook(__FILE__,'Story_management::activate');

// initialize the plugin
$image_management = new Story_management();
$image_management->init();

class Story_management
{
	private $_author_admin;
	private $_location_admin;
	private $_excerpt_admin;
	private $_magazineFeatured_admin;
	private $_specialFeatured_admin;
	
   public function __construct()
   {
		$this->_author_admin           = new SM_author_admin();
		$this->_location_admin         = new SM_location_admin();
		$this->_excerpt_admin          = new SM_content_admin();
		$this->_magazineFeatured_admin = new SM_magazineFeatured_admin();
		$this->_specialFeatured_admin = new SM_special_featured_admin();
   }

	public static function activate()
	{
		SM_author_admin::activate();
		SM_location_admin::activate();
		SM_content_admin::activate();
	}

   	public function init()
   	{
		// What page are we on now?
//		global $pagenow;

		// Run author, location, and other hooks, only on new/edit story pages.
//		if ( 'post.php' == $pagenow || 'post-new.php' != $pagenow  || 'admin-ajax.php' != $pagenow ){
	   		$this->_hook_actions();
			$this->_hook_filters();
//		}
	}

   private function _hook_actions()
   {
		$this->_magazineFeatured_admin->hook_actions();
		$this->_specialFeatured_admin->hook_actions();
		$this->_author_admin->hook_actions();
		$this->_location_admin->hook_actions();
		$this->_excerpt_admin->hook_actions();
   }

	private function _hook_filters()
	{
		$this->_author_admin->hook_filters();
		$this->_location_admin->hook_filters();
		$this->_excerpt_admin->hook_filters();
	}
}
