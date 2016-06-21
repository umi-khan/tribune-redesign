<?php

/**
 * Description of author_adminclass
 *
 * @author ovais.tariq
 */
class SM_location_admin
{
	public static function activate()
	{
		global $wpdb;

		// update the postmeta key name
		$sql = "UPDATE {$wpdb->postmeta} SET meta_key = '" . SM_location_manager::META_KEY . "' WHERE meta_key = 'sm_post_location'";
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
			add_action( 'wp_ajax_sm_add_location', array( $this, 'add_location' ) );
			add_action( 'wp_ajax_sm_remove_location', array( $this, 'remove_location' ) );
		}
	}

	public function hook_filters()
	{
		if(is_admin())
		{

		}
		else
		{
			add_filter( 'the_content', array( $this, 'prepend_location_to_content' ) );
		}
	}

	public function prepend_location_to_content($content)
	{
		global $post;

		$location_manager = new SM_location_manager( $post->ID );

		$post_locations = implode( ' / ', (array)$location_manager->locations );

		if( false == $post_locations ) return $content;

		$location = sprintf( "<strong class='location'>%s:&nbsp;</strong>", strtoupper( $post_locations ) );

		return preg_replace( "/(<[^>]+>)([^<])/", "$1$location$2", $content, 1 );
	}

	//////// admin related

	public function init_admin()
	{
		// dependencies
		wp_register_script( 'jquery-autocomplete', SM_PLUGIN_URL . 'js/jquery.autocomplete.js' );
		wp_register_script( 'sm-autocomplete', SM_PLUGIN_URL . 'js/custom_autocomplete.js' );

		wp_register_style( 'jquery-autocomplete',  SM_PLUGIN_URL . 'css/jquery.autocomplete' );

		add_meta_box( 'sm_location_metabox', 'Locations (auto-saved on change)', array( $this, 'display_metabox' ),
				  'post', 'side', 'high' );
	}

	public function init_admin_client_scripts()
	{
		$this->_set_location_info_js();

		wp_enqueue_script( 'sm-location-admin', SM_PLUGIN_URL . 'js/location.js',
				  array( 'jquery', 'jquery-autocomplete', 'sm-autocomplete' ), '1.1' );
	}

	public function init_stylesheets()
	{
		wp_enqueue_style( 'sm-location-admin', SM_PLUGIN_URL . 'css/location_admin.css', array( 'jquery-autocomplete' ) );
	}

	public function display_metabox()
	{
		global $post_ID, $temp_ID;
		$post_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);
		
		include SM_PLUGIN_DIR . 'templates/location_metabox.php';
	}

	//////////////////// admin ajax actions

	public function add_location()
	{
		$post_id  = (int)$_REQUEST['sm_post_id'];
		$location = trim( $_REQUEST['sm_item'] );

		$response        = new stdClass();
		$response->error = 'There was an error adding the location to current post.';

		$post_locations = new SM_location_manager( $post_id, false );

		if( false == ( $location = $post_locations->add( $location ) ) ) die( json_encode( $response ) );

		$response->error      = null;
		$response->item       = new stdClass();
		$response->item->id   = $location;
		$response->item->name = $location;

		die( json_encode( $response ) );
	}

	public function remove_location()
	{
		$post_id  = (int)$_REQUEST['sm_post_id'];
		$location = trim( $_REQUEST['sm_item'] );

		$response        = new stdClass();
		$response->error = 'There was an error removing the location from current post.';

		$post_locations = new SM_location_manager( $post_id, false );

		if( false == ( $location = $post_locations->remove( $location ) ) ) die( json_encode( $response ) );

		$response->error      = null;
		$response->item       = new stdClass();
		$response->item->id   = $location;
		$response->item->name = $location;

		die( json_encode( $response ) );
	}

	//////////////////////// helper function
	
	private function _set_location_info_js()
	{
		global $post_ID, $temp_ID;
		$post_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);

		$post_locations = new SM_location_manager( $post_id );
		
		$current_locations = array();
		foreach( (array)$post_locations->locations as $location ) $current_locations[] = array( 'id' => $location, 'name' => $location );

		// all locations
		$locations = SM_location_manager::get_locations();

		$all_locations = array();
		foreach( (array)$locations as $location ) $all_locations[] = array( 'id' => $location, 'name' => $location );

		?>

		<script type='text/javascript'>
		/* <![CDATA[ */
			var SM_location_info = {
				current_locations : <?php echo json_encode( $current_locations ); ?> ,
				all_locations     : <?php echo json_encode( $all_locations ); ?>
			};
		/* ]]> */
		</script>

		<?php
	}
}