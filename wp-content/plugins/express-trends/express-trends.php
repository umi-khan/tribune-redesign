<?php

/*
 * Plugin Name: Express Trends
 * Description: A plugin that shows the trending stories and uses a novel mechanism to score the stories
 * Author: Express Media
 * Author URI: http://express.com.pk/
 * Plugin URI: http://tribune.com.pk/
 * Version: 1.0
 */

define('ET_PLUGIN_DIR', trailingslashit(WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__))));
define('ET_PLUGIN_URL', trailingslashit(WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__))));

// include required files
include_once ET_PLUGIN_DIR . 'lib/admin.class.php';
include_once ET_PLUGIN_DIR . 'lib/trends.class.php';

// set up the plugin activation hook
register_activation_hook(__FILE__, array( 'Express_trends', 'activate') );

// setup the admin related stuff
$et_plugin = new Express_trends();
$et_plugin->init();

/**
 * A class that fetches the trending stories by a particular tag and then displays the stories
 */
class Express_trends
{
	public static function activate()
	{
		global $wpdb;

		// create the view that finds posts by tags
		$view_name = $wpdb->prefix . ET_trends::TAG_POSTS_VIEW;
		$create_view_sql = "
			CREATE
			OR REPLACE
			ALGORITHM = MERGE
			VIEW $view_name
			AS
			SELECT p.*, t.*
			FROM wp_posts AS p
				  INNER JOIN wp_term_relationships AS tr ON p.ID = tr.object_id
				  INNER JOIN wp_term_taxonomy      AS tt USING(term_taxonomy_id)
				  INNER JOIN wp_terms              AS t  USING(term_id)
			WHERE tt.taxonomy = 'post_tag'";

		if( false === $wpdb->query( $create_view_sql ) ) wp_die( 'Could not create a required view!' );

		return true;
	}

	private $_admin;

	public function __construct()
	{
		$this->_admin = new ET_admin();
	}

	public function init()
	{
		$this->_hook_actions();
		$this->_hook_filters();

		$this->_admin->init();
	}

	public function init_client_scripts()
	{
		wp_enqueue_script( 'ajax-xmlrpc', ET_PLUGIN_URL . 'js/xmlrpc.js', array( 'jquery' ), '1.0' );
		wp_enqueue_script( 'et-main', ET_PLUGIN_URL . 'js/main.js', array( 'jquery', 'ajax-xmlrpc' ), '1.0' );

		wp_localize_script( 'et-main', 'ET_config', array( 'ajax_url' => WP_SITEURL . "/xmlrpc.php" ) );
	}

	public function init_xml_rpc_methods($wp_rpc_methods)
	{
		$wp_rpc_methods['et_more_stories'] = array( $this, 'display_more_stories' );

		return $wp_rpc_methods;
	}

	public function display_more_stories($params)
	{
		$trend_slug = trim( $params['slug'] );
		$offset     = (int)$params['offset'];

		$trending = new ET_trends( $trend_slug, 0, 15 );

		// clean the output buffer
		while(@ob_end_clean());

		header("Content-Type: text/html");

		echo $trending->fetch_page( $offset );

		exit;
	}

	private function _hook_actions()
	{
		if( is_admin() )
		{

		}
		else
		{
			add_action( 'wp_print_scripts', array( $this, 'init_client_scripts' ) );
		}
	}

	private function _hook_filters()
	{
		if( is_admin() )
		{

		}
		else
		{
			add_filter( 'xmlrpc_methods',  array( $this, 'init_xml_rpc_methods' ) );
		}
	}
}