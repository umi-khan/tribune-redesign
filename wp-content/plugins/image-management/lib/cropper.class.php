<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of imageeditorclass
 *
 * @author ovais.tariq
 */
class IM_cropper
{
	const IMAGE_QUALITY = 70;

	public function __construct()
	{

	}

	// resize the image based on the zoom percentage
	// if the save path is provided the image is saved on that path, else the image's binary data is returned
	public function resize($src_image, $zoom, $save_path = false)
	{
		// if the image does not exist then do nothing
		if( ! file_exists($src_image) )
		{
			return false;
		}

		// intialize the original image details such as width, height, quality, etc
		extract( $this->_init( $src_image ) );

		// setup the resized sized based on the zoom percentage
		$resized_width  = round( $width * $zoom / 100, 0 );
		$resized_height = round( $height * $zoom / 100, 0 );

		// setup the original image so that it can be used by the cropping / resizing functions
		$original_image = call_user_func( $creation_function, $src_image );

		$resized_image = $this->_create_canvas( $resized_width, $resized_height );

		if( false == $resized_image ) return false;

		// setup image transparency if image type is png
		if( $mime_type == 'image/png' || $mime_type == 'image/x-png' ) $this->_init_transparency($resized_image);

		// copy the image data in the resized image canvas
		ImageCopyResampled($resized_image, $original_image, 0, 0, 0, 0, $resized_width, $resized_height, $width, $height);

		// destroy the resource that holds the original image because we dont need it now
		@imagedestroy( $original_image );

		// if a path has been given to which the image has to be saved then save the image
		if($save_path)
		{
			// create the resized image and save it
			call_user_func($output_function, $resized_image, $save_path, $quality);

			@imagedestroy( $resized_image );

			return true;
		}
		else // a path was not given then the image data is returned
		{
			// Put the data of the resized image into a variable
			ob_start();

			// create the resized image and output its contents
			call_user_func($output_function, $resized_image, null, $quality);

			$image_data	= ob_get_contents();

			ob_end_clean();

			@imagedestroy( $resized_image );

			return $image_data;
		}
	}

	// crop the image based on the offset
	// if the save path is provided the image is saved on that path, else the image's binary data is returned
	public function crop($src_image, $offset_x, $offset_y, $crop_width, $crop_height, $save_path = false)
	{
		// if the image does not exist then do nothing
		if( ! file_exists($src_image) )
		{
			return false;
		}

		// intialize the original image details such as width, height, quality, etc
		extract( $this->_init( $src_image ) );

		// do some sanity checks
		$offset_x = max( round( $offset_x, 0 ), 0 );
		$offset_y = max( round( $offset_y, 0 ), 0 );

		// setup the original image so that it can be used by the cropping / resizing functions
		$original_image = call_user_func( $creation_function, $src_image );

		$resized_image = $this->_create_canvas( $crop_width, $crop_height );

		if( false == $resized_image ) return false;

		// setup image transparency if image type is png
		if( $mime_type == 'image/png' || $mime_type == 'image/x-png' ) $this->_init_transparency($resized_image);

		// copy the image data in the resized image canvas
		imagecopy($resized_image, $original_image, 0, 0, $offset_x, $offset_y, $crop_width, $crop_height);

		// destroy the resource that holds the original image because we dont need it now
		@imagedestroy( $original_image );

		// if a path has been given to which the image has to be saved then save the image
		if($save_path)
		{
			// create the resized image and save it
			call_user_func($output_function, $resized_image, $save_path, $quality);

			@imagedestroy( $resized_image );

			return true;
		}
		else // a path was not given then the image data is returned
		{
			// Put the data of the resized image into a variable
			ob_start();

			// create the resized image and output its contents
			call_user_func($output_function, $resized_image, null, $quality);

			$image_data	= ob_get_contents();

			ob_end_clean();

			@imagedestroy( $resized_image );

			return $image_data;
		}
	}

	// http://drupal.org/node/254500
	// rescaled images, especially small thumbnail images, look rather blurred compared to the original.
	// Any graphics library or graphics program which use antialiasing when resampling images
	// create more or less blurred small images
	// http://loriweb.pair.com/8udf-sharpen.html for the matrix, divisor and offset for image sharpening filter
	public function sharpen($src_image)
	{
		// if the image does not exist then do nothing
		if( ! file_exists($src_image) )
		{
			return false;
		}

		// intialize the original image details such as width, height, quality, etc
		extract( $this->_init( $src_image ) );

		// setup the original image so that it can be used by the cropping / resizing functions
		$image = call_user_func( $creation_function, $src_image );
		
		$sharpen_matrix = array(
				  array(-1, -1, -1),
				  array(-1, 16, -1),
				  array(-1, -1, -1)
		);

		$divisor	= 8;
		$offset	= 0;

		imageconvolution($image, $sharpen_matrix, $divisor, $offset);

		return true;
	}

	// initialize the original image, setup the size & type of original image
	// the image creation functions also depend on the image type so they are initialized here too
	private function _init($src_image)
	{
		// if the image does not exist then do nothing
		if( ! file_exists( $src_image ) )
		{
			return false;
		}

		// get the size and type of the image
		$image_details = getimagesize( $src_image );

		$original_width    = $image_details[0];
		$original_height   = $image_details[1];
		$image_type        = $image_details['mime'];
		$quality           = self::IMAGE_QUALITY;
		$do_sharpen        = true;
		$creation_function = 'imagecreatefromjpeg';
		$output_function	 = 'imagejpeg';

		// setup the image manipulation functions and quality of the image and whether image sharpening is required or not
		switch( $image_type )
		{
			case 'image/gif':
			// Need to convert GIFs to PNGs because gd has some problems with resizing transparent gifs and messes them
				$image_type			 = 'image/png';
				$creation_function = 'imagecreatefromgif';
				$output_function	 = 'imagepng';
				// In case of PNG the quality is Compression level: from 0 (no compression) to 9, so we have to scale down
				// the quality to the scale of 0 to 9
				$quality			   = round( 10 - ( self::IMAGE_QUALITY / 10 ) );
				break;

			case 'image/x-png':
			case 'image/png':
				$creation_function = 'imagecreatefrompng';
				$output_function	 = 'imagepng';
				// In case of PNG the quality is Compression level: from 0 (no compression) to 9, so we have to scale down
				// the quality to the scale of 0 to 9
				$quality			    = round( 10 - ( self::IMAGE_QUALITY / 10 ) );
				break;

			default:
				$creation_function = 'imagecreatefromjpeg';
				$output_function	 = 'imagejpeg';
				// incase of jpeg the quality ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file)
				$quality           = self::IMAGE_QUALITY;
				break;
		}

		return array(
				  'width'             => $original_width,
				  'height'            => $original_height,
				  'mime_type'         => $image_type,
				  'creation_function' => $creation_function,
				  'output_function'   => $output_function,
				  'do_sharpen'        => $do_sharpen,
				  'quality'           => $quality
		);
	}

	private function _create_canvas( $width, $height )
	{
		if( $width < 1 || $height < 1 ) return false;
		
		// create an empty image canvas to which the data has to be copied
		$resized_image = imagecreatetruecolor( $width, $height );

		return $resized_image;
	}

	// attempt to save full alpha channel information (as opposed to single-color transparency) when saving PNG images.
	private function _init_transparency( $image )
	{
		// Turn off alpha blending and set alpha flag
		imagealphablending( $image, false );
		imagesavealpha( $image, true );
	}
}
