<?php

/*
 * Plugin Name: Express Stories Layout Management
 * Description: A plugin that lets users manage the layout of the stories.
 * Author: Express Media
 * Author URI: http://express.com.pk/
 * Plugin URI: http://tribune.com.pk/
 * Version: 1.0
 */

define('LAYOUT_MANAGEMENT_PLUGIN_DIR', trailingslashit(WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__))));
define('LAYOUT_MANAGEMENT_PLUGIN_URL', trailingslashit(WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__))));

/*
 * including required files/classes for the plugin
 */
require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'lib/config.class.php';
require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'lib/editor.class.php';
require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'lib/story.class.php';
require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'lib/layout.class.php';
require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'lib/admin.class.php';
require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'lib/html_json_response_helper.class.php';
require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'lib/rpc.class.php';
require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'lib/cache.class.php';
require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'lib/cookies.class.php';
require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'lib/user.class.php';

/*
 * including widget classes
 */
require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'widgets/category-stories-widget.class.php';

// set up the plugin activation hook
register_activation_hook(__FILE__,'Layout_management::install');

// initialize the plugin
$LAYOUT_MANAGEMENT = new Layout_management();
$LAYOUT_MANAGEMENT->init();

/**
 * This is the main plugin file, that does the following:
 * 1. install the layout management table if its not already installed,
 * 2. hooks up to all the required actions and filters based on whether the request is for an admin page or a regular page
 * 3. initialze the layout management popup
 * 4. setup the xmlrpc methods that will be called by ajax
 * 5. remove the default wordpress loop because we dont use it
 *
 * @package Layout_management
 */
class Layout_management
{
   private $_layout_editor;
   private $_current_user;
	
	public $admin;
	
	public $category_id;   
   
   public $layout_groups = array();

	public static function install()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . LM_config::TABLE_NAME;

		if( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) return true;

		$sql = 'CREATE TABLE `wp_layout_management` (
					  `post_id` bigint(11) NOT NULL,
					  `category_id` bigint(11) NOT NULL,
					  `group_id` int(3) NOT NULL,
					  `position` int(3) NOT NULL,
					  PRIMARY KEY  (`post_id`,`category_id`,`group_id`),
					  UNIQUE KEY `category_id` (`category_id`,`group_id`,`position`)
					) ENGINE=MYISAM DEFAULT CHARSET=latin1
					';

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		add_option( 'lm_db_version', '1.0' );
	}
   
   public function __construct()
   {
		$this->category_id = $this->get_category_id();
		
		$this->admin = new LM_admin();
   }

   public function init()
   {
   	$this->_hook_actions();

		$this->_hook_filters();
	}

	/**
	 * This function hooks to the appropriate actions, if the request is for an admin page, the admin class is asked to
	 * handle the action hooking.
	 * Hooks to the following actions if the request is not for an admin page:
	 * 1. wp_head - to include the stylesheets required by the layout manager popup on the frontend
	 * 2. wp_print_scripts - to include the javascript files required by the layout manager popup on the frontend
	 * 3. wp_footer - to create the popup and append it to the html in the footer
	 */
   private function _hook_actions()
   {
   	if( is_admin() )
   	{
			$this->admin->hook_actions();
   	}
   	else
   	{
			add_action( 'wp_head', array( $this, 'init_stylesheets' ) );
			add_action( 'wp_print_scripts', array( $this, 'init_client_scripts' ) );			
			add_action( 'wp_footer', array( $this, 'init_popup' ) );
   	}
   }

	/**
	 * This function hooks to the apprpriate filters, if the request if for an admin page, the admin class is asked to
	 * handle the filter hooking.
	 * Hooks to the following filters if the request is not for an admin page:
	 * 1. xmlrpc_methods - to make the methods visible to xmlrpc call
	 * 2. request - to disable the default wordpress loop
	 */
   private function _hook_filters()
   {
		if( is_admin() )
   	{
			$this->admin->hook_filters();
   	}
		else
		{
			add_filter( "xmlrpc_methods",  array( $this, "init_xml_rpc_methods" ) );
			add_filter( 'request', array( $this, 'handle_wp_request' ) );
		}
   }

	/**
	 * This function makes changes to the query variables used by the default wordpress loop. The default wordpress loops
	 * fetches 10 posts ordered by date, together with the count of all the posts.
	 * 
	 * We don't make use of this information on the category and sub-category pages, because we have our own way of 
	 * rendering these pages.
	 * 
	 * But the default loop is used on the authors pages and the single story pages.
	 * 
	 * So we modify the query variables only when the request is for a category page. The modifications that we make are
	 * such as removing orderby, removing groupby, removing the part that fetches the total number of posts and limiting
	 * the result set to zero rows which actually stops MySQL from executing this query. These adjustments mitigate the
	 * performance penalty introduced by this default query.
	 *
	 * @param array $query_vars An array of variables used to create a query for wordpress default loop
	 * @return array An array of query variables modified according to our needs. The query variables array has to be
	 * returned by this function at all cost, otherwise wordpress will not be able to handle requests.
	 */
	public function handle_wp_request($query_vars)
	{
		if( false === $this->get_category_id() || $this->is_editor_logged_in() ) return $query_vars;

		$query_vars['no_found_rows']  = true;
		$query_vars['orderby']        = 'none';
		$query_vars['paged']          = 0;
		$query_vars['posts_per_page'] = 1;

		add_filter( 'posts_groupby', array( $this, 'remove_groupby' ) );

		return $query_vars;
	}

	public function remove_groupby($groupby)
	{
		return '';
	}

	public function remove_limits($limits)
	{
		return ' LIMIT 0, 0 ';
	}

   public function init_client_scripts()
   {
		if( false == $this->is_editor_logged_in() ) return;
   	
		// json-xml-rpc related client library
		wp_enqueue_script('xmlrpc-util', LAYOUT_MANAGEMENT_PLUGIN_URL . 'js/xmlrpc.js');
		wp_enqueue_script('jquery-json', LAYOUT_MANAGEMENT_PLUGIN_URL . 'js/jquery.json.js', array('jquery'));
		wp_enqueue_script('xml-json-rpc-helper', LAYOUT_MANAGEMENT_PLUGIN_URL . 'js/xml_json_rpc_helper.js',
				  array('xmlrpc-util', 'jquery-json'));

		// excerpt related library
		wp_enqueue_script( 'jquery-charslimit', SM_PLUGIN_URL . 'js/jquery.chars_limit.js', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-spotlight', SM_PLUGIN_URL . 'js/jquery.spotlight.js', array( 'jquery' ), '1.1' );

		wp_enqueue_script('lm-rpc', LAYOUT_MANAGEMENT_PLUGIN_URL . 'js/rpc_handler.js', array('xml-json-rpc-helper'));

		wp_localize_script('lm-rpc', 'LM_config', array( 'base_url' => WP_SITEURL,
			'current_category_id' => $this->category_id, 'excerpt_max_length' => LM_config::STORY_EXCERPT_LENGTH ) );

		wp_enqueue_script('lm-tabs', LAYOUT_MANAGEMENT_PLUGIN_URL . 'js/jquery.tools.tabs.js', array('jquery'));

		// layout management main library
		wp_enqueue_script('lm-main', LAYOUT_MANAGEMENT_PLUGIN_URL . 'js/layout_management.js', array('jquery',
			'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-dialog', 'lm-rpc', 'lm-tabs', 'jquery-charslimit', 'jquery-spotlight'), '1.5');
   }

   public function init_stylesheets()
   {
		if( false == $this->is_editor_logged_in() ) return;

		echo '<link rel="stylesheet" href="'.LAYOUT_MANAGEMENT_PLUGIN_URL.'css/editor.css" type="text/css" />';
		echo '<link rel="stylesheet" href="'.LAYOUT_MANAGEMENT_PLUGIN_URL.'css/jquery-ui.css?v=1.8.10" type="text/css" />';
   }
   
   public function init_popup()
   {
		if( false == $this->is_editor_logged_in() ) return;

		if( ! ( $this->_layout_editor instanceof LM_editor ) ) $this->_layout_editor = new LM_editor();

		$this->_layout_editor->create_popup();
   }
   
	/*
	 * This function is the filter applied on xmlrpc methods of wordpress. It will add functions of layoutmanager to
	 * XmlRpc methods array of wordpress, so that we can call these functions using ajax calls from javascript. All the
	 * methods that are to be exposed to xmlrpc calls are defined in the LM_rpc class as public methods, and their names
	 * are fetched using reflection.
	 *
	 * @param array $wp_rpc_methods array of functions exposed by wordpress to xmlrpc calls
	 * @return array Include the functions that we want to expose to the array of functions exposed to xmlrpc calls.
	 * This array should be returned at all costs otherwise, xmlrpc functionality of wordpress will not work.
	 */
	public function init_xml_rpc_methods($wp_rpc_methods)
	{
		$reflection = new ReflectionClass( 'LM_rpc' );
		$lm_rpc_methods = $reflection->getMethods( ReflectionMethod::IS_PUBLIC );		//fetching all public method names
		
		$lm_rpc = new LM_rpc();
		
		foreach( $lm_rpc_methods as $lm_rpc_method )
		{
			if( $lm_rpc_method instanceof ReflectionMethod && $lm_rpc_method->name != '__construct' )
				$wp_rpc_methods[$lm_rpc_method->name] = array( $lm_rpc, $lm_rpc_method->name );
		}
	
   	return $wp_rpc_methods;
	}

   private function is_editor_logged_in()
   {
	  static $editor_logged_in = NULL;

	  if($editor_logged_in === NULL)
	  {
		  $this->_current_user = new LM_user();

		  $editor_logged_in = $this->_current_user->is_editor();
	  }
	  
	  return $editor_logged_in;
   }

   private function get_category_id()
   {
		if( is_home() ) return 0;

		if( is_category() )
		{
			global $wp_query;
			$category = $wp_query->get_queried_object();

			return $category->cat_ID;
		}

		return false;
   }
}