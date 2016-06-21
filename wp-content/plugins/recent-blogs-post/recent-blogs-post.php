<?php

/*
Plugin Name: Recent Blog Posts
Description: This Plugin fetches the recent blog posts from the Tribune Blogs and shows them as a widget
Author: Express Media
Author URI: http://express.com.pk/
Plugin URI: http://tribune.com.pk/
Version: 1.0
*/

define( 'RECENTBLOGS_PLUGIN_DIR', trailingslashit( WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) ) );
define( 'RECENTBLOGS_PLUGIN_URL', trailingslashit( WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) ) );

require_once RECENTBLOGS_PLUGIN_DIR . 'lib/manager.class.php';
require_once RECENTBLOGS_PLUGIN_DIR . 'lib/cache.class.php';
require_once RECENTBLOGS_PLUGIN_DIR . 'lib/widget.class.php';

Recent_Blog_Posts::init();

class Recent_Blog_Posts
{
	public static function init()
	{	
		add_action( 'widgets_init', array( 'Recent_Blogs_Widget', 'register' ) );
	}
}