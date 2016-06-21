<?php

/**
 * Description of image_editorclass
 *
 * @author ovais.tariq
 */
class IM_editor
{	
	public function hook_actions()
	{
		// perform actions on admin initialization
		add_action( 'admin_init', array( $this, 'init_admin' ) );

		// insert all the required javascripts
		add_action( 'admin_print_scripts-post.php', array( $this, 'init_client_scripts' ) );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'init_client_scripts' ) );
		add_action( 'admin_print_scripts-media_page_im-media-library', array( $this, 'init_client_scripts' ) );

		// ajax actions
		add_action( 'wp_ajax_im_display_editor', array( $this, 'display_editor' ) );
		add_action( 'wp_ajax_im_save_image', array( $this, 'save_image' ) );

		// empty the image sizes array
		add_filter( 'intermediate_image_sizes_advanced', array( $this, 'empty_image_sizes' ) );
	}

	public function init_admin()
	{
		// dependencies
		wp_register_script( 'jquery-ui-slider', IMAGE_MANAGEMENT_PLUGIN_URL . 'js/jquery.ui.slider.js', array(), false, true );
		wp_register_script( 'jquery-uploader', IMAGE_MANAGEMENT_PLUGIN_URL . 'js/jquery.upload.js', array(), false, true );

		// cropper script
		wp_register_script( 'im-image-resizer', IMAGE_MANAGEMENT_PLUGIN_URL . 'js/jquery.image_resizer.js',
				array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse',
					'jquery-ui-draggable', 'jquery-ui-resizable', 'jquery-ui-slider' ), '1.82', true );
	}

	public function init_client_scripts()
	{
		wp_enqueue_script( 'im-editor', IMAGE_MANAGEMENT_PLUGIN_URL . 'js/editor.js',
				array( 'jquery', 'im-image-resizer', 'im-popup' ), '1.8' , true );
	}

	// this function will empty the image sizes array so that wordpress does not automatically crop/resize the image
	public function empty_image_sizes($image_sizes)
	{
		return array();
	}

	public function display_editor()
	{
		// setup the image details
		$image         = new IM_image_attachment( $_POST['image_id'] );
		$image_sizes   = IM_image_attachment::get_sizes();
		$image_html_id = 'img_' . $image->id;

		// show the editor
		include IMAGE_MANAGEMENT_PLUGIN_DIR . 'templates/editor.php';

		// stop the script execution
		die();
	}

	// save the resized / cropped image, image title and caption, and the resized dimensions and coordinates
	public function save_image()
	{
		$image   = new IM_image_attachment( $_POST['image_id'] );
		$title   = esc_attr( $_POST['title'] );
		$caption = esc_attr( $_POST['caption'] );
		
		if( $title )   $image->title   = esc_attr( $_POST['title'] );
		if( $caption ) $image->caption = esc_attr( $_POST['caption'] );

		$this->_sanitize_dimensions($_POST['crop_info'], 3);

		// set the crop info
		$image->large_crop_info = array(
			'zoom'     => $_POST['crop_info']['zoom'],
			'width'    => $_POST['crop_info']['width'],
			'height'   => $_POST['crop_info']['height'],
			'offset_x' => $_POST['crop_info']['offset_x'],
			'offset_y' => $_POST['crop_info']['offset_y']
		);
		$image->thumb_crop_info = array(
			'thumb_zoom'         => $_POST['crop_info']['thumb_zoom'],
			'thumb_offset_x'     => $_POST['crop_info']['thumb_offset_x'],
			'thumb_offset_y'     => $_POST['crop_info']['thumb_offset_y'],
			'thumb_box_width'    => $_POST['crop_info']['thumb_box_width'],
			'thumb_box_height'   => $_POST['crop_info']['thumb_box_height'],
			'thumb_box_offset_x' => $_POST['crop_info']['thumb_box_offset_x'],
			'thumb_box_offset_y' => $_POST['crop_info']['thumb_box_offset_y']
		);

		$suffixes = IM_image_attachment::get_suffixes();
		$sizes = array(
			'thumb' => array( 'w' => $_POST['crop_info']['thumb_width'], 'h' => $_POST['crop_info']['thumb_height'] ),
			'large' => array( 'w' => $_POST['crop_info']['large_width'], 'h' => $_POST['crop_info']['large_height'] )
		);

		// setup the image meta
		$meta = $image->meta;

		// setup the file path of the original image that is to be resized
		$uploads    = wp_upload_dir();
		$image_path = realpath( $uploads['basedir'] . "/{$meta['file']}" );

		// delete the previous versions of thumbnail and large images if they exist
		// we are doing this to invaldiate browser cache, because our images are cached for month
		// and if change the same image, the changes are not visible to the user
		if( is_array( $meta['sizes'] ) )
		{
			$image_info = pathinfo( $image_path );
			$image_dir  = $image_info['dirname'];

			$thumb_path = "{$image_dir}/{$meta['sizes']['thumbnail']['file']}";
			$large_path = "{$image_dir}/{$meta['sizes']['large']['file']}";

			@unlink( $thumb_path );
			@unlink( $large_path );
		}

		// do the resizing
		$info = $this->_resize_image($image_path, $suffixes, $sizes, $_POST['crop_info']);

		// the sizes of thumb and large image has to be saved in the wpdb
		$meta['sizes'] = array(
			'thumbnail' => array( 'file'  => $info['thumb'], 'width' => $sizes['thumb']['w'], 'height' => $sizes['thumb']['h'] ),
			'large'     => array( 'file' => $info['large'], 'width' => $sizes['large']['w'], 'height' => $sizes['large']['h'] )
		);

		$image->meta = $meta;

		// save the image details
		$image->save();

		// send the response as json object and stop the script execution
		$response = new stdClass();
		$response->error    = null;
		$response->image_id = $image->id;

		die( json_encode( $response ) );
	}

	/////////////////// helper functions

	// round off the values
	private function _sanitize_dimensions(&$dimensions, $precision)
	{
		$dimensions['zoom']     = round($dimensions['zoom'], $precision);
		$dimensions['width']    = round($dimensions['width'], $precision);
		$dimensions['height']   = round($dimensions['height'], $precision);
		$dimensions['offset_x'] = round($dimensions['offset_x'], $precision);
		$dimensions['offset_y'] = round($dimensions['offset_y'], $precision);

		$dimensions['thumb_zoom']     = round($dimensions['thumb_zoom'], $precision);
		$dimensions['thumb_offset_x'] = round($dimensions['thumb_offset_x'], $precision);
		$dimensions['thumb_offset_y'] = round($dimensions['thumb_offset_y'], $precision);

		$dimensions['thumb_box_width']    = round($dimensions['thumb_box_width'], $precision);
		$dimensions['thumb_box_height']   = round($dimensions['thumb_box_height'], $precision);
		$dimensions['thumb_box_offset_x'] = round($dimensions['thumb_box_offset_x'], $precision);
		$dimensions['thumb_box_offset_y'] = round($dimensions['thumb_box_offset_y'], $precision);
	}

	// resize the image and save the resized image on disk
	private function _resize_image($full_image_path, $suffixes, $sizes, $crop_info)
	{
		// get the image info
		$image_info = pathinfo( $full_image_path );
		$image_dir  = $image_info['dirname'];
		$image_ext  = $image_info['extension'];
		$image_name = basename( $full_image_path, ".{$image_ext}" );

		// get the time in microseconds because it will be appended to the filename to generate a unique filename
		$random_num = mt_rand( 100, 1000 );

		// the name of the large image
		$large_name = "{$image_name}-{$random_num}-{$suffixes['large']}.{$image_ext}";
		$large_path = "{$image_dir}/{$large_name}";

		// the name of the thumbnail
		$thumb_name = "{$image_name}-{$random_num}-{$suffixes['thumb']}.{$image_ext}";
		$thumb_path = "{$image_dir}/{$thumb_name}";

		// setup the image resizer
		$image_resizer = new IM_cropper();

		// create the large image
		$image_resizer->resize($full_image_path, $crop_info['zoom'], $large_path);
		$image_resizer->crop($large_path, $crop_info['offset_x'], $crop_info['offset_y'],
				  $sizes['large']['w'], $sizes['large']['h'], $large_path);

		// create the thumbnail
		$image_resizer->resize($full_image_path, $crop_info['thumb_zoom'], $thumb_path);
		$image_resizer->crop($thumb_path, $crop_info['thumb_offset_x'], $crop_info['thumb_offset_y'],
				  $sizes['thumb']['w'], $sizes['thumb']['h'], $thumb_path);

		return array(
			'thumb' => $thumb_name,
			'large' => $large_name
		);
	}
}