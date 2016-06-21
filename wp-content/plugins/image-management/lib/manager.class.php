<?php

/**
 * This class is used to perform image related operations on any post, these operations include fetching all the
 * attached images, fetching the default image, uploading an image, deleting an image, setting an image as default, etc.
 *
 * @author ovais.tariq
 */
class IM_Manager
{
	const KEY_DEFAULT_IMAGE = '_im_default_image';
	const KEY_UPLOAD_FILE   = 'im_image_file';

	private $post_id;
	
	/**
	 *
	 * @var array contains all the images associated with the post
	 */
	public $images;
	/**
	 *
	 * @var IM_image_attachment contains the default image, its an object of the class IM_image_attachment
	 */
	public $default_image;

	private $_no_image;

	/**
	 * @param int $post_id The id of the post
	 * @param bool $load_data If this is set to true, all the images associated with the post is loaded into an array.
	 * Set it to true only when you want to display all the images in a post. Set it to false when you want to perform
	 * any operation like upload, delete or when you want to fetch the default image.
	 */
	public function __construct($post_id, $load_data = true)
	{
		$this->post_id       = (int) $post_id;
		$this->_no_image     = false;
		$this->default_image = $this->_get_default();

		if( $load_data ) $this->_load();
	}
	
	public function upload()
	{
		if ( empty($_FILES) ) return false;

		$overrides = array(
			'unique_filename_callback' => array( $this, 'generate_unique_filename' ),
			'test_form' => false
		);

		// upload the file
		$image_id = media_handle_upload( self::KEY_UPLOAD_FILE, $this->post_id, array(), $overrides );
		unset( $_FILES );
		if ( is_wp_error( $image_id ) ) return false;

		// if there is no default image set, set this one as the default image
		if( false == $this->default_image || $this->_no_image ) $this->set_default( $image_id );

		return $image_id;
	}

	public function add($image_or_id)
	{
		if( false == ( $image_or_id instanceof IM_image_attachment ) )
			$image_or_id = new IM_image_attachment( $image_or_id );

		$image_id = $image_or_id->id;

		// if the image is already attached to the current post do nothing
		if( $this->has( $image_or_id ) ) return false;

		// get the upload directory info
		$uploads = $this->_get_uploads_dir();

		// get the image meta and image path info
		$meta     = wp_get_attachment_metadata( $image_id );
		$filepath = realpath( untrailingslashit( $uploads['basedir'] ) . '/' . ltrim($meta['file'], '/') );
		$filename = basename( $filepath );

		// if the original file which is to be copied does not exist then exit
		if( false == file_exists( $filepath ) ) return false;

		// generate the new path
		$new_filename = wp_unique_filename( $uploads['path'], $filename, array( $this, 'generate_unique_filename' ) );
		$new_filepath = realpath( $uploads['path'] ) . "/$new_filename";
		$new_url      = $uploads['url'] . "/$new_filename";

		// copy the full image
		$this->_copy_image( $filename, $filepath, $new_filepath );
		
		// get the image details
		$image = get_post( $image_id, ARRAY_A );
		
		// get the crop info
		$crop_info = get_post_meta( $image_id, IM_image_attachment::KEY_CROP_INFO, true );
		
		// prepare the data for the new attachment
		unset( $image['ID'], $image['post_date'], $image['post_date_gmt'], $image['post_author'], $image['post_parent'] );
		$image['guid'] = $new_url;

		// setup the attachment meta data
		$meta['file'] = untrailingslashit( ltrim( $uploads['subdir'], '/' ) ) . '/' . $new_filename;

		if( is_array( $meta['sizes'] ) && count( $meta['sizes'] ) >= 2 )
		{
			$new_thumb_info = $this->_copy_image( $meta['sizes']['thumbnail']['file'], $filepath, $new_filepath, 'thumbnail' );
			$new_large_info = $this->_copy_image( $meta['sizes']['large']['file'], $filepath, $new_filepath, 'large' );

			$meta['sizes']['thumbnail']['file'] = $new_thumb_info['name'];
			$meta['sizes']['large']['file']     = $new_large_info['name'];
		}

		// insert the image
		if( false == ( $new_image_id = wp_insert_attachment( $image, $meta['file'], $this->post_id ) ) ) return false;

		// save the new image object details
		$image_attachment = new IM_image_attachment( $new_image_id, $image['post_title'], $image['post_excerpt'], $this->post_id );

		$image_attachment->thumb_crop_info = $crop_info['thumb'];
		$image_attachment->large_crop_info = $crop_info['large'];
		$image_attachment->meta            = $meta;
		
		if( false == $image_attachment->save() ) return false;

		return $image_attachment;
	}

	public function delete($image_or_id)
	{
		if( false == ( $image_or_id instanceof IM_image_attachment ) ) $image_or_id = new IM_image_attachment( $image_or_id );
		
		if( false == $this->has( $image_or_id ) ) return false;

		if( false == $image_or_id->delete() ) return false;

		// if this is the default image, unset it and set the latest image as the default one
		if( $this->is_default( $image_or_id ) )
		{
			// set the latest image as the default one
			if( ! is_array( $this->images ) || count( $this->images ) < 1 )
			{				
				delete_post_meta( $this->post_id, self::KEY_DEFAULT_IMAGE, $image_or_id->id );
				$this->_load();
			}
			else	$this->set_default( $this->images[0] );
		}

		return true;
	}

	// is the passed image a child of this post
	public function has($image_or_id)
	{
		if( false === $this->post_id ) return false;

		if( false == ( $image_or_id instanceof IM_image_attachment ) ) $image_or_id = new IM_image_attachment( $image_or_id );
		
		return ( $image_or_id->parent_id == $this->post_id );
	}

	// set this image as the default image
	public function set_default($image_or_id)
	{
		if( false == $this->post_id || false == $image_or_id ) return false;
		
		$image_id = ( $image_or_id instanceof IM_image_attachment ) ? $image_or_id->id : $image_or_id;
		
		if( ! update_post_meta( $this->post_id, self::KEY_DEFAULT_IMAGE, $image_id ) )
		{
			add_post_meta( $this->post_id, self::KEY_DEFAULT_IMAGE, $image_id, true );
		}

		if( $image_or_id instanceof IM_image_attachment )
			$this->default_image = $image_or_id;
		else
			$this->default_image = new IM_image_attachment($image_or_id);

		$this->_no_image = false;

		return true;
	}

	public function is_default($image_or_id)
	{
		$image_id = ( $image_or_id instanceof IM_image_attachment ) ? $image_or_id->id : $image_or_id;

		return ( $image_id == $this->default_image->id );
	}

	public function generate_unique_filename($dir, $name, $ext)
	{
		// get the time in microseconds because it will be appended to the filename to generate a unique filename
		$usec = time();

		// sanitize the image name
		$name = preg_replace( "/[^a-zA-Z_]/", '', $name );
		
		// prevent empty filename for the image and add a simple placeholder
		if (trim($name) == '')
			$name = 'image';

		return "{$this->post_id}-{$name}-{$usec}{$ext}";
	}

	/////////// helper functions
	
	private function _load()
	{
		if( false === $this->post_id ) return false;

		if( is_array( $this->images ) && count( $this->images ) > 0 ) return true;

		$args = 'post_parent=' . $this->post_id . '&post_type=attachment&post_mime_type=image&orderby=date&order=DESC';
		
		$post_images   = get_children( $args );

		foreach( (array)$post_images as $image )
		{			
			$this->images[] = new IM_image_attachment($image->ID, $image->post_title, $image->post_excerpt, $this->post_id );
		}

		return true;
	}

	private function _get_default()
	{
		if( false == $this->post_id ) return false;

		$default_image = get_post_meta( $this->post_id, self::KEY_DEFAULT_IMAGE, true );

		if( false == $default_image )
		{
			// if there is no default image set, set the latest one as the default image and return it
			$this->_load();

			if( is_array( $this->images ) && count( $this->images ) > 0 )
			{
				$this->set_default( $this->images[0] );

				return $this->images[0];
			}
			else // there are no images associated with this post so returning a dummy image
			{
				$this->_no_image = true;
				
				return new IM_image_attachment( -1, '', '', $this->post_id );
			}
		}

		return new IM_image_attachment( $default_image );
	}

	private function _get_uploads_dir()
	{
		// we need the post date to find out the directory where the image has to be saved to
		$time = current_time( 'mysql' );
		if ( $post = get_post( $this->post_id ) )
		{
			if ( substr( $post->post_date, 0, 4 ) > 0 ) $time = $post->post_date;
		}

		return wp_upload_dir( $time );
	}

	private function _copy_image($filename_to_copy, $path_to_copy_from, $path_to_copy_to, $image_type='full')
	{
		switch( $image_type )
		{
			case 'full':
				// copy the old image to the new image path
				copy( $path_to_copy_from, $path_to_copy_to );

				return array(
					'path' => $path_to_copy_to,
					'name' => basename( $path_to_copy_to )
				);

			case 'thumbnail':
				// get the thumbnail of the original image
				$image_info     = pathinfo( $path_to_copy_from );
				$dir_name       = $image_info['dirname'];
				$image_ext      = $image_info['extension'];
				$thumbnail_path = $dir_name . '/' . $filename_to_copy;
				
				// get the thumbnail suffix
				$suffixes = IM_image_attachment::get_suffixes();

				// generate new thumbnail name
				$new_image_info = pathinfo( $path_to_copy_to );
				$new_image_name = $new_image_info['filename'];
				$new_thumb_name = "{$new_image_name}-{$suffixes['thumb']}.{$image_ext}";
				$new_thumb_path = $new_image_info['dirname'] . "/$new_thumb_name";

				// copy the old image to the new image path
				copy( $thumbnail_path, $new_thumb_path );
				
				return array(
					'path' => $new_thumb_path,
					'name' => $new_thumb_name
				);

			case 'large':
				// get the large image name of the original image
				$image_info     = pathinfo( $path_to_copy_from );
				$dir_name       = $image_info['dirname'];
				$image_ext      = $image_info['extension'];
				$large_path     = $dir_name . '/' . $filename_to_copy;

				// get the large image suffix
				$suffixes = IM_image_attachment::get_suffixes();

				// generate new large image name
				$new_image_info = pathinfo( $path_to_copy_to );
				$new_image_name = $new_image_info['filename'];
				$new_large_name = "{$new_image_name}-{$suffixes['large']}.{$image_ext}";
				$new_large_path = $new_image_info['dirname'] . "/$new_large_name";

				// copy the old large image to the new large image path
				copy( $large_path, $new_large_path );

				return array(
					'path' => $new_large_path,
					'name' => $new_large_name
				);
		}	
	}

	public function has_images()
	{		
		return ( false === $this->_no_image );
	}
}