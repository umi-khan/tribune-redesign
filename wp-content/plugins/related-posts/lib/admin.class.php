<?php

/**
 * Description of adminclass
 *
 * @author ovais.tariq
 */
class ERP_admin
{
	const NUM_SEARCH_RESULTS = 10;
	const MAX_PAGES          = 10;

	public function hook_actions()
	{
		if( is_admin() )
		{
			// perform actions on admin initialization
			add_action( 'admin_init', array( $this, 'init_admin' ) );

			// insert all the required javascripts
			add_action( 'admin_print_scripts', array( $this, 'init_admin_js' ) );

			// insert all the required css
			add_action( 'admin_print_styles', array( $this, 'init_stylesheets' ) );

			// ajax actions
			add_action( 'wp_ajax_erp_do_search', array( $this, 'do_search' ) );
			add_action( 'wp_ajax_erp_save_selections', array( $this, 'save_selections' ) );
		}
	}

	public function hook_filters()
	{
		
	}

	public function init_admin()
	{
		add_meta_box( 'erp_metabox', 'Related Posts', array( $this, 'display_metabox' ), 'post', 'advanced' );
	}

	public function init_admin_js()
	{
		wp_enqueue_script( 'erp-search', ERP_PLUGIN_URL . 'js/search.js',
				array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-sortable' ), '1.2' );
	}

	public function init_stylesheets()
	{
		wp_enqueue_style( 'erp-admin', ERP_PLUGIN_URL . 'css/admin.css', array(), '1.2' );
	}

	public function display_metabox()
	{
		global $post_ID, $temp_ID;
		$post_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);

		$related_posts = Express_related_posts::get_related_posts( $post_id, false );

		include ERP_PLUGIN_DIR . 'templates/metabox.php';
	}

	public function do_search()
	{
		$post_id     = (int)( $_REQUEST['erp_post_id'] );
		$q           = stripslashes( trim( $_REQUEST['erp_search_text'] ) );
		$duration    = (int)$_REQUEST['erp_duration'];
		$page_num    = (int)$_REQUEST['erp_page_num'];

		// we will search for one more than the number of records we will display
		// and if there are more records than those to be displayed, that means we need to show the next page button
		$num_results = self::NUM_SEARCH_RESULTS + 1;
		
		$search  = new ERP_search( $q );
		$results = $search->get_results( $post_id, $duration, $page_num, $num_results );

		// setup if we need to show the next and previous page buttons
		$show_previous_link = false;
		if( $page_num > 0 ) $show_previous_link = true;

		$show_next_link = false;
		if( $page_num < ( self::MAX_PAGES - 1 ) && count( $results ) > self::NUM_SEARCH_RESULTS ) $show_next_link = true;

		// setup the record numbers
		$record_num_start = $page_num * 10 + 1;
		$record_num_end   = $page_num * 10 + self::NUM_SEARCH_RESULTS;

		// fetch only the number of records we will be displaying and ignore the one extra record
		$results = array_slice( $results, 0, self::NUM_SEARCH_RESULTS );
		
		include ERP_PLUGIN_DIR . 'templates/search_results.php';

		die();
	}

	public function save_selections()
	{
		$post_id       = (int)( $_REQUEST['erp_post_id'] );
		$related_posts = trim( $_REQUEST['erp_related_posts'] );

		$response = new stdClass();
		$response->error = null;

		// do some error handling and response sanitization
		if( strlen( $related_posts ) < 1 ) $response->error = 'Select at least one related post';

		$related_posts = array_map( 'intval', (array)explode( ',', $related_posts ) );

		if( count( $related_posts ) < 1 ) $response->error = 'Select at least one related post';

		if( $response->error ) die( json_encode( $response ) );

		// now insert the related posts in the order specified
		global $wpdb;

		$table_name = Express_related_posts::get_tablename();

		// delete the old related posts first
		$delete_sql = "DELETE FROM $table_name WHERE post_id = $post_id";
		if( false === $wpdb->query( $delete_sql ) )
		{
			$response->error = 'An internal error occurred';
			die( json_encode( $response ) );
		}

		// insert the related posts
		$insert_sql = "
			INSERT INTO wp_erp_related_posts(post_id, related_post_id, `order`)
			VALUES ";

		$order = 1;
		$num_related_posts = count( $related_posts );
		foreach( (array)$related_posts as $rp )
		{
			$insert_sql .= "($post_id, $rp, $order)";

			if( $order < $num_related_posts ) $insert_sql .= ",";
			
			$order++;
		}

		if( false === $wpdb->query( $insert_sql ) ) $response->error = 'Could not save related posts';

		do_action( 'edit_post', $post_id, get_post( $post_id ) );

		die( json_encode( $response ) );
	}
}