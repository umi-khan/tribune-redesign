<?php

/**
 * Description of mediaclass
 *
 * @author ovais.tariq
 */
class IM_library
{
	const ITEMS_PER_PAGE = 20;

	public function hook_actions()
	{
		// perform actions on admin initialization
		add_action( 'admin_init', array( $this, 'init_admin' ) );
		
		// add menu items
		add_action( 'admin_menu', array( $this, 'init_menu' ) );

		// ajax actions
		add_action( 'wp_ajax_im_display_media_library', array( $this, 'display_ajax' ) );
		add_action( 'wp_ajax_im_library_upload_image', array( $this, 'upload_image' ) );
		add_action( 'wp_ajax_im_library_display_image', array( $this, 'display_image' ) );
		add_action( 'wp_ajax_im_library_add_image', array( $this, 'add_image' ) );

		// insert all the required javascripts
		add_action( 'admin_print_scripts-post.php', array( $this, 'init_client_scripts' ) );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'init_client_scripts' ) );
		add_action( 'admin_print_scripts-media_page_im-media-library', array( $this, 'init_client_scripts' ) );
	}

	public function init_admin()
	{
		wp_register_script( 'jquery-uploader', IMAGE_MANAGEMENT_PLUGIN_URL . 'js/jquery.upload.js' );
	}

	public function init_client_scripts()
	{
		wp_enqueue_script( 'im-library', IMAGE_MANAGEMENT_PLUGIN_URL .	'js/library.js',
				  array( 'jquery', 'im-popup', 'jquery-uploader' ), '1.5', true );
	}

	public function init_menu()
	{
		// remove the media menus so that all the handling is done by the image management plugin
		global $submenu;
		foreach( (array)$submenu['upload.php'] as $key=>$value ) unset( $submenu['upload.php'][$key] );

		add_media_page( 'IM Library', 'Library', 5, 'im-media-library', array( $this, 'display_page' ) );
	}

	public function display_page()
	{
		$this->_display();
	}

	public function display_ajax()
	{
		$this->_display( true );
	}

	public function upload_image()
	{
		$post_images  = new IM_Manager( $_POST['post_id'], false );
		$upload_error = null;

		if( ! $image_id = $post_images->upload() )
		{
			$image_id     = null;
			$upload_error = 'The image could not be uploaded.';
		}

		$response = new stdClass();
		$response->image_id = $image_id;
		$response->error    = $upload_error;

		// send the response as json object and stop the script execution
		die( json_encode( $response ) );
	}

	public function add_image()
	{
		$post_images = new IM_Manager( $_POST['post_id'], false );
		$image_id    = null;
		$error       = null;

		$image = $post_images->add( (int) $_POST['image_id'] );

		if( ! ( $image instanceof IM_image_attachment ) ) $error = 'The image could not be added to post.';
		else $image_id = $image->id;

		$response = new stdClass();
		$response->image_id = $image_id;
		$response->error    = $error;

		// send the response as json object and stop the script execution
		die( json_encode( $response ) );
	}

	public function display_image()
	{
		$image = new IM_image_attachment( $_POST['image_id'] );

		// add the resized image to the story images gallery
		include IMAGE_MANAGEMENT_PLUGIN_DIR . 'templates/library_image_details.php';

		// stop the script execution
		die();
	}

	/////////////////////// helper functions
	private function _display($is_ajax_request = false)
	{
		// setup the details
		global $wpdb, $wp_query, $wp_locale, $post_mime_types, $type, $tab;

		$post_id = (int) $_REQUEST['post_id'];
		$type    = 'image';
		$tab     = 'library';

		// for mime type filtering
		$_GET['post_mime_type'] = 'image';
		$post_mime_type         = 'image';

		// for pagination
		$_GET['paged'] = isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 0;
		if ( $_GET['paged'] < 1 ) $_GET['paged'] = 1;

		$start = ( $_GET['paged'] - 1 ) * self::ITEMS_PER_PAGE;
		if ( $start < 1 ) $start = 0;

		add_filter( 'post_limits', create_function( '$a', "return 'LIMIT $start, " . self::ITEMS_PER_PAGE . "';" ) );

		// for filtering of images already present in the current post being edited
		$this->_filter_images( $post_id );

		// query the wpdb and fetch all the image attachments
		list( $post_mime_types, $avail_post_mime_types ) = wp_edit_attachments_query();

		// show the media library
		include IMAGE_MANAGEMENT_PLUGIN_DIR . 'templates/media_library.php';

		// stop the script execution
		if( false !== $is_ajax_request ) die();
	}

	// for filtering of images already present in the current post being edited
	private function _filter_images($post_id)
	{
		global $wpdb;
		
		if( false == $post_id ) return;
		
		$image_manager = new IM_manager( $post_id );
		$images = $image_manager->images;

		$image_ids = array();
		foreach( (array) $images as $image )
			$image_ids[] = $image->id;

		if( is_array( $image_ids ) && count( $image_ids ) > 0 )
		{
			$image_ids = implode( ',', $image_ids );

			$filter_statement = 'return $w . ' . "\"AND ({$wpdb->posts}.ID NOT IN ($image_ids))\"" . ';';
			add_filter( 'posts_where', create_function( '$w', $filter_statement ) );
		}
	}
}