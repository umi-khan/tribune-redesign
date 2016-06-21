<?php

/*
 * Plugin Name: Express Stories Image Management
 * Description: A plugin that lets users manage the images attached to stories.
 * Author: Express Media
 * Author URI: http://express.com.pk/
 * Plugin URI: http://tribune.com.pk/
 * Version: 1.0
 */

define('IMAGE_MANAGEMENT_PLUGIN_DIR', trailingslashit(WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__))));
define('IMAGE_MANAGEMENT_PLUGIN_URL', trailingslashit(WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__))));

/*
 * including required files/classes for the plugin
 */
require_once IMAGE_MANAGEMENT_PLUGIN_DIR . 'lib/gallery.class.php';
require_once IMAGE_MANAGEMENT_PLUGIN_DIR . 'lib/editor.class.php';
require_once IMAGE_MANAGEMENT_PLUGIN_DIR . 'lib/library.class.php';
require_once IMAGE_MANAGEMENT_PLUGIN_DIR . 'lib/cropper.class.php';
require_once IMAGE_MANAGEMENT_PLUGIN_DIR . 'lib/manager.class.php';
require_once IMAGE_MANAGEMENT_PLUGIN_DIR . 'lib/image.class.php';

// initialize the plugin
$image_management = new Image_Management();
$image_management->init();

class Image_Management
{
	public $gallery;
	public $editor;
	public $library;
	
   public function __construct()
   {
		if( is_admin() )
		{
			$this->gallery = new IM_gallery();
			$this->editor  = new IM_editor();
			$this->library = new IM_library();
		}
   }

   public function init()
   {
   	$this->_hook_actions();
	}

   private function _hook_actions()
   {
   	if(is_admin())
   	{
			// image gallery actions
			$this->gallery->hook_actions();

			// image editor actions
			$this->editor->hook_actions();

			// media library actions
			$this->library->hook_actions();
   	}
   }
   
   /**
   * gets the current post type in the WordPress Admin
   */
   public static function get_current_post_type() {
      global $post, $typenow, $current_screen, $pagenow;
      
      $current_post_type = null;
      //we have a post so we can just get the post type from that
      if ($post && $post->post_type) $current_post_type = $post->post_type;
      //check the global $typenow - set in admin.php
      elseif ($typenow) $current_post_type = $typenow;
      //check the global $current_screen object - set in sceen.php
      elseif ($current_screen && $current_screen->post_type) $current_post_type = $current_screen->post_type;
      //lastly check the post_type querystring
      elseif (isset($_REQUEST['post_type'])) $current_post_type = sanitize_key($_REQUEST['post_type']);
      //we do not know the post type!
      elseif( $pagenow == "post-new.php" ) $current_post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
      elseif( $pagenow == "post.php" && $_REQUEST['action'] == 'edit' ) $current_post_type = get_post_type( $_REQUEST['post'] );
         
      return $current_post_type;
   }
}