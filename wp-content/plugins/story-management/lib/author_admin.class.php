<?php

/**
 * Description of author_adminclass
 *
 * @author ovais.tariq
 */
class SM_author_admin
{
	public static function activate()
	{
		global $wpdb;

		//@@todo remove this in the next version
		$sql = "UPDATE {$wpdb->postmeta} SET meta_key = '" . SM_author_manager::META_KEY . "' WHERE meta_key = 'sm_post_author' ";

		$wpdb->query( $sql );
	}

	public function hook_actions()
	{
		if( is_admin() )
		{
			// perform actions on admin initialization
			add_action( 'admin_init', array( $this, 'init_admin' ) );

			// insert all the required javascripts
			add_action( 'admin_print_scripts', array( $this, 'init_admin_client_scripts' ) );

			// insert all the required css
			add_action( 'admin_print_styles', array( $this, 'init_stylesheets' ) );

			// ajax actions
			add_action( 'wp_ajax_sm_add_author', array( $this, 'add_author' ) );
			add_action( 'wp_ajax_sm_remove_author', array( $this, 'remove_author' ) );
		}
		else
		{
			add_action( 'wp_print_scripts', array( $this, 'init_client_scripts' ) );
		}
	}

	public function hook_filters()
	{
		if( is_admin() )
		{
			
		}
		else
		{
			add_filter( 'posts_join', array( $this, 'add_author_join' ) );
			add_filter( 'posts_where', array( $this, 'remove_author_where' ) );
			add_filter( 'the_author', array( $this, 'the_author' ) );
			add_filter( 'the_author_posts_link', array( $this, 'the_author_posts_link' ) );
			add_filter( 'wp_title', array( $this, 'wp_title' ) );
			add_filter( 'xmlrpc_methods',  array( $this, 'init_xml_rpc_methods' ) );
		}
	}

	/////// frontend related actions/filters
	public function add_author_join($sql_join)
	{
		global $wpdb;

		$current_author = get_query_var( 'author' );

		if( false == is_author() || false == $current_author ) return $sql_join;

		$sql_join .= " INNER JOIN {$wpdb->postmeta} ON
				{$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
				AND {$wpdb->postmeta}.meta_key = '" . SM_author_manager::META_KEY . "'
				AND {$wpdb->postmeta}.meta_value = '" . $current_author . "'";

		return $sql_join;
	}

	public function remove_author_where($sql_where)
	{
		global $wpdb;
		
		$author = get_query_var( 'author' );

		if( false == is_author() || false == $author ) return $sql_where;

		return str_ireplace( "AND ($wpdb->posts.post_author = $author)", '', $sql_where );
	}

	public function the_author($author)
	{
		global $post;

		$post_authors = new SM_author_manager( $post->ID );

		$authors = array();
		foreach( (array)$post_authors->authors as $a )
		{
			if( preg_match( "/\bexpress\b/i", $a->name ) ) continue;

			$authors[] = $a->name;
		}

		if( count( $authors ) > 0 ) $author = implode( ' / ', $authors );

		return $author;
	}

	public function the_author_posts_link($link, $post_id = false)
	{
		global $post;

		if( false == $post_id ) $post_id = $post->ID;

		$authors_links = SM_author_manager::get_author_posts_link( $post_id );
		if( $authors_links ) $link = $authors_links;

		return $link;
	}

	public function wp_title($title)
	{
		global $authordata;
		
		if( false == is_author() ) return $title;

		$author = new SM_author( $authordata->ID, $authordata->wp_capabilities, $authordata->first_name, 
				  $authordata->last_name, $authordata->nickname, $authordata->user_nicename );

		return str_replace( $authordata->user_login, $author->name, $title );
	}

	public function init_xml_rpc_methods($wp_rpc_methods)
	{
		$wp_rpc_methods['sm_authors_get_list'] = array( $this, 'get_authors_list' );
		
		return $wp_rpc_methods;
	}

	public function get_authors_list($alphabet)
	{
		// clean the output buffer
		while(@ob_end_clean());

		header("Content-Type: text/html");

		SM_author_manager::list_authors( $alphabet );

		exit;
	}

	public function init_client_scripts()
	{
		if( false == is_author() ) return;
		
		wp_enqueue_script( 'xmlrpc-util', SM_PLUGIN_URL . 'js/xmlrpc.js' );
		wp_enqueue_script( 'jquery-json', SM_PLUGIN_URL . 'js/jquery.json.js', array( 'jquery' ) );
		wp_enqueue_script( 'xml-json-rpc-helper', SM_PLUGIN_URL . 'js/xml_json_rpc_helper.js',
				  array( 'xmlrpc-util', 'jquery-json' ) );
		
		wp_enqueue_script( 'sm-author-nav', SM_PLUGIN_URL . 'js/author_navigation.js',
				  array( 'jquery', 'xml-json-rpc-helper' ), '1.1' );

		wp_localize_script( 'sm-author-nav', 'SM_author_config', array( 'base_url' => WP_SITEURL ) );
	}

	/////// admin related actions/filters /////////////////////////////
	public function init_admin()
	{
		// dependencies
		wp_register_script( 'jquery-autocomplete', SM_PLUGIN_URL . 'js/jquery.autocomplete.js' );
		wp_register_script( 'sm-autocomplete', SM_PLUGIN_URL . 'js/custom_autocomplete.js' );

		wp_register_style( 'jquery-autocomplete',  SM_PLUGIN_URL . 'css/jquery.autocomplete.css' );

		add_meta_box( 'sm_author_metabox', 'Authors (auto-saved on change)', array( $this, 'display_metabox' ),
				  'post', 'side', 'high' );
	}

	public function init_admin_client_scripts()
	{
		$this->_set_authors_info_js();

		wp_enqueue_script( 'sm-author-admin', SM_PLUGIN_URL . 'js/author.js',
				  array( 'jquery', 'jquery-autocomplete', 'sm-autocomplete' ), '1.0' );
	}

	public function init_stylesheets()
	{
		wp_enqueue_style( 'sm-author-admin', SM_PLUGIN_URL . 'css/author_admin.css', array( 'jquery-autocomplete' ) );
	}

	public function display_metabox()
	{
		global $post_ID, $temp_ID;
		$post_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);
		
		include SM_PLUGIN_DIR . 'templates/author_metabox.php';
	}

	public function add_author()
	{
		$post_id = (int)$_REQUEST['sm_post_id'];
		$author  = trim( $_REQUEST['sm_item'] );
		
		$response        = new stdClass();
		$response->error = 'There was an error adding the author to current post.';

		$post_authors = new SM_author_manager( $post_id, false );
		
		if( false == ( $author = $post_authors->add( $author ) ) ) die( json_encode( $response ) );

		$response->error      = null;
		$response->item       = new stdClass();
		$response->item->id   = $author->id;
		$response->item->name = $author->name;

		die( json_encode( $response ) );
	}

	public function remove_author()
	{
		$post_id   = (int)$_REQUEST['sm_post_id'];
		$author_id = (int)$_REQUEST['sm_item'];

		$response        = new stdClass();
		$response->error = 'There was an error removing the author from current post.';

		$post_authors = new SM_author_manager( $post_id, false );

		if( false == ( $author = $post_authors->remove( $author_id ) ) ) die( json_encode( $response ) );

		$response->error      = null;
		$response->item       = new stdClass();
		$response->item->id   = $author->id;
		$response->item->name = $author->name;

		die( json_encode( $response ) );
	}

	/////// helper methods /////////////////////////////////////////////
	private function _set_authors_info_js()
	{
		global $post_ID, $temp_ID;
		$post_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);
		
		$post_authors = new SM_author_manager( $post_id );

		// current authors related
		$current_authors = array();
		foreach( (array)$post_authors->authors as $author )
			$current_authors[] = array( 'id' => $author->id, 'name' => $author->name );

		// all authors related
		$authors = SM_author_manager::get_authors();

		$all_authors = array();
		foreach( (array)$authors as $author ) $all_authors[] = array( 'id' => $author->id, 'name' => $author->name );

		?>

		<script type='text/javascript'>
		/* <![CDATA[ */
			var SM_author_info = {
				current_authors : <?php echo json_encode( $current_authors ); ?> ,
				all_authors     : <?php echo json_encode( $all_authors ); ?>
			};
		/* ]]> */
		</script>

		<?php
	}
}