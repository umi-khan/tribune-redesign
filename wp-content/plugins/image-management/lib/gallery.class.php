<?php

class IM_gallery
{
	public function hook_actions()
	{
		// perform actions on admin initialization
		add_action( 'admin_init', array( $this, 'init_admin' ) );

		// insert all the required javascripts
		add_action( 'admin_print_scripts-post.php', array( $this, 'init_client_scripts' ) );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'init_client_scripts' ) );
		add_action( 'admin_print_scripts-media_page_im-media-library', array( $this, 'init_client_scripts' ) );

		// insert all the required css
		add_action( 'admin_print_styles-post.php', array( $this, 'init_stylesheets' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'init_stylesheets' ) );
		add_action( 'admin_print_styles-media_page_im-media-library', array( $this, 'init_stylesheets' ) );

		// ajax actions
		add_action( 'wp_ajax_im_display_image', array( $this, 'get_image_html' ) );
		add_action( 'wp_ajax_im_delete_image', array( $this, 'delete_image' ) );
		add_action( 'wp_ajax_im_set_default_image', array( $this, 'set_default_image' ) );
	}

	public function init_admin()
	{
		// dependencies
		wp_register_script( 'jquery-tools-tooltip', IMAGE_MANAGEMENT_PLUGIN_URL . 'js/jquery.tools.tooltip.js' );
		
		// popup script
		wp_register_script( 'im-popup', IMAGE_MANAGEMENT_PLUGIN_URL . 'js/popup.js',
				array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-dialog' ), '1.4', true );

		// gallery script
		wp_register_script( 'im-gallery', IMAGE_MANAGEMENT_PLUGIN_URL . 'js/gallery.js',
				array( 'jquery', 'jquery-tools-tooltip' ), '1.8', true );

      $current_post_type = Image_Management::get_current_post_type();
      
      if ( !is_null($current_post_type) && !in_array( $current_post_type, array( 'page', 'attachment', 'revision', 'nav_menu_item' ) )  ) 
         add_meta_box('im_metabox', __( 'Images Gallery (auto-saved on change)', 'image-management' ), array( $this, 'display_metabox' ), 'slideshow', 'normal', 'high');
      		

		remove_all_actions('media_buttons');
	}

	public function init_client_scripts()
	{
		wp_enqueue_script( 'im-main', IMAGE_MANAGEMENT_PLUGIN_URL . 'js/main.js',
				array( 'jquery', 'im-popup', 'im-gallery' ), '1.4', true );
	}

	public function init_stylesheets()
   {
      wp_enqueue_style( 'im-style', IMAGE_MANAGEMENT_PLUGIN_URL.'css/style.css', array(), '1.8'  );
      wp_enqueue_style( 'jquery-ui', IMAGE_MANAGEMENT_PLUGIN_URL.'css/jquery-ui.css', array(), '1.8'  );		
   }

	public function display_metabox()
	{
		global $post_ID, $temp_ID;
		$post_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);

		$post_images = new IM_Manager( $post_id );

		include IMAGE_MANAGEMENT_PLUGIN_DIR . 'templates/metabox.php';
	}

	public function get_image_html()
	{
		$image = new IM_image_attachment( $_POST['image_id'] );

		// add the resized image to the story images gallery
		include IMAGE_MANAGEMENT_PLUGIN_DIR . 'templates/image_details.php';

		// stop the script execution
		die();
	}

	public function set_default_image()
	{
		$error       = 'The image could not be set as default!';
		$post_images = new IM_Manager( $_POST['post_id'], false );

		if( $post_images->has( $_POST['image_id'] ) )
		{
			$post_images->set_default( $_POST['image_id'] );
			$error = null;
		}

		$response = new stdClass();
		$response->error = $error;

		// send the response as json object and stop the script execution
		die( json_encode( $response ) );
	}

	public function delete_image()
	{
		$post_images = new IM_Manager( $_POST['post_id'], false );

		$error = ( $post_images->delete( $_POST['image_id'] ) ) ? null : 'The image could not be deleted.';

		$response = new stdClass();
		$response->error = $error;

		// send the response as json object and stop the script execution
		die( json_encode( $response ) );
	}
}