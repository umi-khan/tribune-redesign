<?php

/**
 * This class holds all the information about a post such as title, caption, parent, thumbnail, large image, etc.
 *
 * @author ovais.tariq
 */
class IM_image_attachment
{
	const KEY_CROP_INFO = '_im_crop_info';
	
	public $id;
	public $title;
	public $caption;
	public $parent_id;

	private $meta;

	private $full;
	private $large;
	private $thumbnail;

	private $thumb_crop_info;
	private $large_crop_info;

	public function __construct($image_id, $title=false, $caption=false, $parent_id=false)
	{
		$this->id         = (int) $image_id;

		if( false === $title || false === $caption || false === $parent_id )
		{
			$image      = get_post( $image_id );
			$title      = $image->post_title;
			$caption    = $image->post_excerpt;
			$parent_id  = $image->post_parent;
		}

		$this->title           = $title;
		$this->caption         = $caption;
		$this->parent_id       = $parent_id;
		$this->meta            = false;
		$this->full            = false;
		$this->large           = false;
		$this->thumbnail       = false;
		$this->thumb_crop_info = false;
		$this->large_crop_info = false;
	}

	public function __get($property)
	{
		$method_name = '_get_' . $property;

		if( method_exists( $this, $method_name ) )
			return $this->$method_name();
	}

	public function __set($property, $value)
	{
		if( $property == 'thumb_crop_info' ) $this->thumb_crop_info = $value;

		if( $property == 'large_crop_info' ) $this->large_crop_info = $value;

		if( $property == 'meta' ) $this->meta = $value;
	}

	public function save()
	{
		// update the image title and caption
		wp_update_post( array( 'ID' => $this->id, 'post_title' => $this->title, 'post_excerpt' => $this->caption ) );

		// update the attachment meta by adding the thumbnail size and the large image size, this data is used by
		// wordpress for displaying the proper image
		wp_update_attachment_metadata( $this->id, $this->meta );

		// save the resized dimensions so that when the image resizer is loaded again it loads at the specified position
		$crop_info = array( 'thumb' => $this->thumb_crop_info, 'large' =>$this->large_crop_info );
		if( ! update_post_meta( $this->id, self::KEY_CROP_INFO, $crop_info ) )
		{
			add_post_meta( $this->id, self::KEY_CROP_INFO, $crop_info, true );
		}
		
		//useful for debugging cropping/resizing issues
		//$this->log( print_r($this->thumb_crop_info, 1) . print_r($this->large_crop_info, 1) );

		return true;
	}

	public function delete()
	{
		return wp_delete_attachment( $this->id );
	}

	public static function get_sizes()
	{
		static $thumb_size = false;
		static $large_size  = false;
		
		// set the thumbnail size
		if( false == $thumb_size )
		{
			$thumb_size = array(
				'w' => get_option( "thumbnail_size_w" ),
				'h' => get_option( "thumbnail_size_h" )
			);
		}

		if( false == $large_size )
		{
			$large_size = array(
				'w' => get_option( "large_size_w" ),
				'h' => get_option( "large_size_h" )
			);
		}

		return array(
			'thumb' => $thumb_size,
			'large' => $large_size
		);
	}

	public static function get_suffixes()
	{
		$sizes = self::get_sizes();

		$thumb_suffix = "{$sizes['thumb']['w']}x{$sizes['thumb']['h']}";
		$large_suffix = "{$sizes['large']['w']}x{$sizes['large']['h']}";

		return array(
			'thumb' => $thumb_suffix,
			'large' => $large_suffix
		);
	}

	public static function is_type_supported($image_type)
	{
		switch( $image_type )
		{
			case 'image/jpeg':
				return ( imagetypes() & IMG_JPG ) != 0;

			case 'image/png':
				return ( imagetypes() & IMG_PNG ) != 0;

			case 'image/gif':
				return ( imagetypes() & IMG_GIF ) != 0;

			default:
				return false;
		}
	}

	///////////////// getter functions
	
	private function _get_thumbnail()
	{
		if( false == $this->thumbnail )
		{
			// if a valid image does not exist return a default image
			if( false == ( $attachment = wp_get_attachment_image_src( $this->id ) ) )
				$attachment = $this->_default_thumbnail();

			list($url, $width, $height) = $attachment;			
			$this->thumbnail = new IM_image($url, $width, $height, $this->caption);
		}

		return $this->thumbnail;
	}

	private function _get_large()
	{
		if( false == $this->large )
		{
			// if a valid image does not exist return a default image
			if( false == ( $attachment = wp_get_attachment_image_src( $this->id, 'large' ) ) )
				$attachment = $this->_default_large();

			list($url, $width, $height) = $attachment;
			$this->large = new IM_image($url, $width, $height, $this->caption);
		}

		return $this->large;
	}

	private function _get_full()
	{
		if( false == $this->full )
		{
			// if a valid image does not exist return a default image
			if( false == ( $attachment = wp_get_attachment_image_src( $this->id, 'full' ) ) )
				$attachment = $this->_default_full();

			list($url, $width, $height) = $attachment;
			$this->full = new IM_image($url, $width, $height, $this->caption);
		}

		return $this->full;
	}

	private function _get_thumb_crop_info()
	{
		if( false == $this->thumb_crop_info )
		{
			$this->_get_crop_info();
		}

		return $this->thumb_crop_info;
	}

	private function _get_large_crop_info()
	{
		if( false == $this->large_crop_info )
		{
			$this->_get_crop_info();
		}

		return $this->large_crop_info;
	}

	private function _get_meta()
	{
		if( false == $this->meta )
		{
			$this->meta = wp_get_attachment_metadata( $this->id );
		}

		return $this->meta;
	}

	////////////////// end of getter functions

	private function _get_crop_info()
	{
		$crop_info = get_post_meta( $this->id, self::KEY_CROP_INFO, true );

		$this->thumb_crop_info = $crop_info['thumb'];
		$this->large_crop_info = $crop_info['large'];
	}

	// default images related, used when no image exists
	private function _default_thumbnail()
	{
		$sizes = self::get_sizes();

		$url    = IMAGE_MANAGEMENT_PLUGIN_URL . 'images/default-thumbnail.gif';
		$width  = $sizes['thumb']['w'];
		$height = $sizes['thumb']['h'];

		return array( $url, $width, $height );
	}

	private function _default_large()
	{
		$sizes = self::get_sizes();

		$url    = IMAGE_MANAGEMENT_PLUGIN_URL . 'images/default-large.gif';
		$width  = $sizes['large']['w'];
		$height = $sizes['large']['h'];

		return array( $url, $width, $height );
	}

	private function _default_full()
	{
		return $this->_default_large();
	}
	
	/*
	//useful for debugging cropping/resizing issues
	protected function log( $str ) {
		error_log( $_SERVER['HTTP_USER_AGENT'] . '::' . $this->meta['file'] . '::' . $str . "\n", 3, IMAGE_MANAGEMENT_PLUGIN_DIR . '/debug.log' );
	}*/
}


class IM_image
{
	public $url;
	public $width;
	public $height;
	public $caption;

	public function __construct($url, $width, $height, $caption)
	{
		$this->url     = $url;
		$this->width   = $width;
		$this->height  = $height;
		$this->caption = $caption;
	}

	public function smart_dimensions($max_width, $max_height)
	{
		$ratio_width  = $max_width / $this->width;
		$ratio_height = $max_height / $this->height;

		$ratio = min( $ratio_width, $ratio_height );

		return array(
			'width'  => round( $this->width * $ratio ),
			'height' => round( $this->height * $ratio )
		);
	}
}