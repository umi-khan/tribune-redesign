<?php

class VM_admin
{
	public function hook_actions()
	{
		// perform actions on admin initialization
		add_action( 'admin_init', array( $this, 'init_admin' ) );

		// insert all the required javascripts		
		add_action( 'admin_print_scripts-post.php', array( $this, 'init_client_scripts' ) );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'init_client_scripts' ) );

		// insert all the required css
		add_action( 'admin_print_styles-post.php', array( $this, 'init_stylesheets' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'init_stylesheets' ) );

		// ajax actions
		$this->_hook_ajax_actions();
	}

	public function init_admin()
	{
		wp_register_script( 'jquery-tools-tooltip', VM_PLUGIN_URL . 'js/jquery.tools.tooltip.js' );
		wp_register_script( 'vm-main', VM_PLUGIN_URL . 'js/main.js', array( 'jquery' ), '1.2', true );

		add_meta_box('vm_metabox', __( 'Videos Gallery (auto-saved on change)', 'post-videos' ),
				  array( $this, 'display_metabox' ), 'post', 'normal', 'high');
	}

	public function init_client_scripts()
	{
		wp_enqueue_script( 'vm-admin', VM_PLUGIN_URL .	'js/admin.js',
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-dialog', 'jquery-tools-tooltip', 'vm-main' ), '1.4' , true );
	}

	public function init_stylesheets()
   {
		wp_register_style( 'vm-style', VM_PLUGIN_URL.  'css/style.css', array(), '1.4' );
		wp_register_style( 'jquery-ui', VM_PLUGIN_URL.  'css/jquery-ui.css', array(), '1.8' );
      
		wp_enqueue_style( 'vm-style');
		wp_enqueue_style( 'jquery-ui');
   }

	private function _hook_ajax_actions()
	{
		add_action( 'wp_ajax_vm_add_video_form', array( $this, 'add_video_form' ) );
		add_action( 'wp_ajax_vm_edit_video_form', array( $this, 'edit_video_form' ) );
		add_action( 'wp_ajax_vm_add_video', array( $this, 'add_video' ) );
		add_action( 'wp_ajax_vm_save_video', array( $this, 'save_video' ) );
		add_action( 'wp_ajax_vm_delete_video', array( $this, 'delete_video' ) );
		add_action( 'wp_ajax_vm_set_default_video', array( $this, 'set_default_video' ) );
		add_action( 'wp_ajax_vm_display_video', array( $this, 'display_video' ) );
		add_action( 'wp_ajax_vm_get_video_from_youtube', array( $this, 'get_video_from_youtube' ) );
	}

	public function display_metabox()
	{
		global $post_ID, $temp_ID;
		$post_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);

		$post_videos = new VM_manager( $post_id );

		include VM_PLUGIN_DIR . 'templates/metabox.php';
	}

	public function add_video_form()
	{
		$post_id = (int) $_POST['post_id'];

		include VM_PLUGIN_DIR . 'templates/add_video.php';

		die();
	}

	public function edit_video_form()
	{
		$video_id = (int) $_POST['video_id'];

		$video = new VM_video( $video_id );

		include VM_PLUGIN_DIR . 'templates/editor.php';

		die();
	}

	public function add_video()
	{
		$post_id   = (int) $_POST['post_id'];
		$video_url = $_POST['video_url'];

		$manager = new VM_manager( $post_id, false );

		$response = new stdClass();

		if( ( $video = $manager->add( $video_url ) ) instanceof VM_video )
		{
			$video_id = $video->id;
			$error    = null;
		}
		else
		{
			$video_id = null;
			$error    = $video;
		}

		$response->video_id = $video_id;
		$response->error    = $error;
		
		die( json_encode($response) );
	}

	public function save_video()
	{
		$video                     = new VM_video( $_POST['video_id'] );
		$video->title              = esc_attr( $_POST['title'] );
		$video->caption            = esc_attr( $_POST['caption'] );

		// save the image details
		$video->save();

		// send the response as json object and stop the script execution
		$response = new stdClass();
		$response->error             = null;
		$response->video_id          = $video->id;
		$response->title             = $video->title;
		$response->caption           = $video->caption;

		die( json_encode($response) );
	}

	public function delete_video()
	{
		$post_videos = new VM_manager( $_POST['post_id'], false );

		$error = ( $post_videos->delete( $_POST['video_id'] ) ) ? null : 'The video could not be deleted.';

		$response = new stdClass();
		$response->error = $error;

		// send the response as json object and stop the script execution
		die( json_encode($response) );
	}

	public function set_default_video()
	{
		$error       = 'The video could not be set as default!';
		$post_videos = new VM_manager( $_POST['post_id'], false );

		if( $post_videos->has( $_POST['video_id'] ) )
		{
			$post_videos->set_default( $_POST['video_id'] );
			$error = null;
		}

		$response = new stdClass();
		$response->error = $error;

		// send the response as json object and stop the script execution
		die( json_encode($response) );
	}

	public function display_video()
	{
		$video_id = (int) $_POST['video_id'];

		$video = new VM_video( $video_id );

		include VM_PLUGIN_DIR . 'templates/video_details.php';

		die();
	}
}