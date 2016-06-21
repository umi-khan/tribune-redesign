<?php

/*
 * Plugin Name: Express Related Posts
 * Description: A plugin that lets users select related posts based on keywords.
 * Author: Express Media
 * Author URI: http://express.com.pk
 * Plugin URI: http://tribune.com.pk
 * Version: 2.0
 */

define( 'ERP_PLUGIN_DIR', trailingslashit( WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) ) );
define( 'ERP_PLUGIN_URL', trailingslashit( WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) ) );

/*
 * including required files/classes for the plugin
 */
require_once ERP_PLUGIN_DIR . 'lib/wordstemmer.class.php';
require_once ERP_PLUGIN_DIR . 'lib/search.class.php';
require_once ERP_PLUGIN_DIR . 'lib/admin.class.php';
require_once ERP_PLUGIN_DIR . 'lib/widget.class.php';

// exposing a global function that will display related posts
if( false == function_exists( 'the_erp_related_posts' ) )
{
	function the_erp_related_posts($post_id = false, $num_posts = Express_related_posts::DEFAULT_NUM_POSTS, $echo = true, $show_date = false, $show_thumbnail = true)
	{
		$post_id   = (int)$post_id;
		$output = '';
		if( false == $post_id )
		{
			global $post;
			$post_id = $post->ID;
		}

		$num_posts = (int)$num_posts;
		if( false == $num_posts ) $num_posts = Express_related_posts::DEFAULT_NUM_POSTS;
		
		$output = Express_related_posts::get_related_posts_html( $post_id, $num_posts, $show_date, $show_thumbnail );
		
		if($echo) echo $output; 
		else return $output;
	}
}

// set up the plugin activation hook
register_activation_hook(__FILE__,'Express_related_posts::activate');

// initialize the plugin
$related_posts_plugin = new Express_related_posts();
$related_posts_plugin->init();

class Express_related_posts
{
	const DEFAULT_NUM_POSTS = 5;
	
	const DB_VERSION_KEY    = 'erp_db_version';
	const DB_VERSION_NUM    = 1.0;

	private static $_tablename = 'erp_related_posts';

	private $_admin;

	public function __construct()
   {
		$this->_admin =  new ERP_admin();
   }

   public function init()
   {
   	$this->_admin->hook_actions();
	}

	public static function get_tablename()
	{
		global $wpdb;

		return $wpdb->prefix . self::$_tablename;
	}

	public static function activate()
	{
		global $wpdb;

		// create fulltext index on excerpt field
		/**if( $wpdb->query( "SHOW INDEX FROM {$wpdb->posts} WHERE Key_name LIKE 'erp_excerpt' AND Index_type = 'FULLTEXT'" ) < 1 )
		{
			$wpdb->query( "CREATE FULLTEXT INDEX erp_excerpt ON {$wpdb->posts}(post_excerpt)" );
		}

		// create composite fulltext index on title, content, excerpt fields
		if( $wpdb->query( "SHOW INDEX FROM {$wpdb->posts} WHERE Key_name LIKE 'erp_title_excerpt_content' AND Index_type = 'FULLTEXT'" ) < 1 )
		{
			$wpdb->query( "CREATE FULLTEXT INDEX erp_title_excerpt_content ON {$wpdb->posts}(post_title, post_excerpt, post_content)" );
		}**/

		// create the table that holds related posts records
		$table_name = self::get_tablename();		
		
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta(  $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
                 `post_id` bigint(20) NOT NULL,
                 `related_post_id` bigint(20) NOT NULL,
                 `order` int(3) NOT NULL,
                 PRIMARY KEY (`post_id`,`related_post_id`,`order`)
               ) ENGINE=MyISAM" );

		//add_option( self::DB_VERSION_KEY, self::DB_VERSION_NUM );
	}

	public static function get_related_posts($post_id, $num_posts = self::DEFAULT_NUM_POSTS)
	{
		$post_id   = (int)$post_id;
		$num_posts = (int)$num_posts;

		if( false == $post_id ) return array();

		global $wpdb;

		$tablename = self::get_tablename();

		$sql = "
			SELECT p.*, `order`
			FROM {$wpdb->posts} AS p INNER JOIN $tablename AS erp ON p.ID = erp.related_post_id
			WHERE erp.post_id = $post_id
			ORDER BY erp.order ASC";

		if( $num_posts > 0 ) $sql .= " LIMIT $num_posts";

		return $wpdb->get_results( $sql );
	}

	public static function get_related_posts_html($post_id, $num_posts = self::DEFAULT_NUM_POSTS, $show_date = false, $show_thumbnail = true)
	{
		$related_posts = self::get_related_posts( $post_id, $num_posts );
		
		if( false == is_array( $related_posts ) || count( $related_posts ) < 1 ) return;
		
			
		$html = '<ul class="links related-stories">';

		$count     = 0;
		$num_posts = count( $related_posts );
		foreach((array) $related_posts as $post)
		{
			$class = ($count == $num_posts - 1) ? ' class="last"' : '';
			
			$html .= '<li' . $class . '>';

			if($show_date)
			{
				$html .= '<span class="date">';
				$html .= mysql2date('d M Y', $post->post_date);
				$html .= '</span>';
			}
			$html .= get_the_post_thumbnail( $post->ID, thumbnail );
			$html .= '<a class="relatedtext" href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>';
			
			$html .= '</li>';

			$count++;
		}

		$html .= '</ul>';

		return $html;
	}
}