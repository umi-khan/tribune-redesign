<?php

/*
 * Plugin Name: Express Stories Video Management
 * Description: A plugin that lets users attach youtube videos to a post
 * Author: Express Media
 * Author URI: http://express.com.pk/
 * Plugin URI: http://tribune.com.pk/
 * Version: 1.0
 */

define('VM_PLUGIN_DIR', trailingslashit(WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__))));
define('VM_PLUGIN_URL', trailingslashit(WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__))));

/*
 * including required files/classes for the plugin
 */
require_once VM_PLUGIN_DIR . 'lib/admin.class.php';
require_once VM_PLUGIN_DIR . 'lib/manager.class.php';
require_once VM_PLUGIN_DIR . 'lib/video.class.php';
require_once VM_PLUGIN_DIR . 'lib/youtube_helper.class.php';
require_once VM_PLUGIN_DIR . 'lib/jw_api.php';

/*
 * including required files/classes for the widget
 */
require_once VM_PLUGIN_DIR . 'lib/recent-videos-widget.class.php';
require_once VM_PLUGIN_DIR . 'lib/related-videos-widget.class.php';

// initialize the plugin
$post_videos_management = new Post_videos();
$post_videos_management->init();

class Post_videos
{
	public $admin;
	
   public function __construct()
   {
		if( is_admin() ) $this->admin = new VM_admin();
   }

   public function init()
   {
   	$this->_hook_actions();

		$this->_register_widget();
	}

   private function _hook_actions()
   {
      global $current_user;
      
		// insert all the required javascripts
		if (! empty( $current_user->ID ) ) add_action( 'wp_print_scripts', array( $this, 'init_client_scripts' ) );
		
   	if(is_admin())
   	{
			// admin related actions
			$this->admin->hook_actions();
   	}
   }

	public function init_client_scripts()
	{
		wp_enqueue_script( 'vm-main', VM_PLUGIN_URL .	'js/main.js', array( 'jquery' ), '1.2' );
	}

	private function _register_widget()
	{
		add_action( 'widgets_init', array( 'VM_Recent_Videos_Widget', 'register' ));
		add_action( 'widgets_init', array( 'VM_Related_Videos_Widget', 'register' ));
	}
}