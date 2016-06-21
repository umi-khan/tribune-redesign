/*
 * @name jQuery plugin for resizing images
 *
 * @author Express Media http://tribune.com.pk
 *
 * @description
 * This plugin resizes an image and keeps the aspectRatio of the image intact
 * The images are resized in two sizes, large size and thumbnail size, these sizes can be specified when calling the
 * resizer function by passing the sizes.
 *
 * @example
 * $.image_resizer({
 			width          : 640,
			height         : 480,
			thumb_width    : 160,
			thumb_height   : 120,
			image_id       : 'xlarge'
		});
 *
 */

(function($)
{

	$.image_resizer = {
		resize           : null,
		get_crop_info    : null
	};

	$.image_resizer.resize = function(settings)
	{
		var img                 = null;
		var viewport            = null;
		var thumb_box           = null;
		var thumb_preview_box   = null;
		var thumb_preview_img   = null;
		var zoom_slider         = null;

		var zoom = {
			max : null,
			min : null
		};

		var options = {
			width        : 640,
			height       : 480,
			thumb_width  : 160,
			thumb_height : 120,
			image_id     : null,
			crop_info    : null
		};

		// if any setting has been provided then override the default settings
		if( settings ) options = $.extend( options, settings );
		
		// if image html id is not provided return from the function
		if( options.image_id == null ) return false;

		// setup the image
		img = $( '#' + options.image_id );

		// if the image is not really an image then we cannot resize, so return from the function
		if( ! img.is( 'img' ) ) return false;

		// initialize the viewport
		init_viewport();
		
		// initialize the image
		// if this is the first time that the image has been loaded for resizing then resize the image to fit the viewport
		init_image();

		// make the image draggable
		make_draggable();

		// initialize the zoom control
		init_zoom_control();

		// initialize the thumbnail box and the thumbnail preview
		init_thumbnail();

		// save the current dimensions of the large image and the thumbnail image
		save_crop_info();

		///// helper functions start here
		
		function init_viewport()
		{
			img.data( 'original_width', img.width() );
			img.data( 'original_height', img.height() );
			
			viewport = $( '#viewport_' + options.image_id );

			var ratio_width_viewport_img  = viewport.width() / img.data( 'original_width' );
			var ratio_height_viewport_img = viewport.height() / img.data( 'original_height' );

			ratio_min = Math.min( ratio_width_viewport_img, ratio_height_viewport_img );
			ratio_max = Math.max( ratio_width_viewport_img, ratio_height_viewport_img );

			viewport.data( 'ratio', ratio_min );
			viewport.data( 'maxratio', ratio_max );
		}

		function init_image()
		{
			var width;
			var height;
			var top  = 0;
			var left = 0;
			
			width  = img.data( 'original_width' ) * viewport.data( 'ratio' );
			height = img.data( 'original_height' ) * viewport.data( 'ratio' );

			if( width < viewport.width() || height < viewport.height() )
			{
				width  = img.data( 'original_width' ) * viewport.data( 'maxratio' );
				height = img.data( 'original_height' ) * viewport.data( 'maxratio' );
			}

			// set the minimum and maximum zoom percentage, the minimum zoom is set so that the minimum level is when
			// the image fits the viewport and the maximum is ofcourse 100
			zoom.min = Math.min( viewport.data( 'ratio' ) * 100, 100 );
			zoom.max = Math.max( viewport.data( 'maxratio' ) * 100, 100 );

			if(options.crop_info)
			{
				width  = options.crop_info.width;
				height = options.crop_info.height;
				top    = - options.crop_info.offset_y;
				left   = - options.crop_info.offset_x;
			}
			
			img.css({
				width  : Math.round(width) + 'px',
				height : Math.round(height) + 'px',
				top    : top + 'px',
				left   : left + 'px'
			});
		}

		function make_draggable()
		{
			img.draggable({
				drag        : function( event, ui )
				{
					update_thumbnail_preview();

					var top  = ui.position.top;
					var left = ui.position.left;
					
					if( ui.position.top > 0 )  top = 0;
					
					if( ui.position.left > 0 ) left = 0;

					if( ui.position.top < ( viewport.height() - img.height() ) )
					{
						if( img.height() > viewport.height() ) top = viewport.height() - img.height();
						else                                   top = 0;
					}

					if( ui.position.left < ( viewport.width() - img.width() ) )
					{
						if( img.width() > viewport.width() ) left = viewport.width() - img.width();
						else                                 left = 0;
					}

					img.css({
						top  : top + 'px',
						left : left + 'px'
					});
					
					ui.position.top  = top;
					ui.position.left = left;
				}
			});
		}

		function init_zoom_control()
		{
			var start_zoom = ( img.width() / img.data( 'original_width' ) ) * 100;

			zoom_slider = $('#zoom_slider_' + options.image_id);

			if(options.crop_info) start_zoom = options.crop_info.zoom;

			zoom_slider.slider({
				orientation : "vertical",
				min         : zoom.min,
				max         : zoom.max,
				value       : start_zoom,
				slide       : function( event, ui )
				{
					var percentage = ui.value / 100;
					
					img.width( Math.round( img.data('original_width')  * percentage ) );
					img.height( Math.round ( img.data('original_height')  * percentage ) );
	
					correct_img_position();
	
					update_thumbnail_preview();
				}
			});

			zoom_slider.css({
				top  : ( viewport.position().top + 10 ) + 'px',
				left : ( viewport.position().left + 10 ) + 'px'
			});
		}

		function init_thumbnail()
		{
			init_thumbnail_preview();
			
			init_thumbnail_box();

			init_thumbnail_btns();

			update_thumbnail_preview();
		}

		function init_thumbnail_preview()
		{
			thumb_preview_box = $( '#thumb_preview_box_' + options.image_id );

			thumb_preview_box.append( img.clone(false).attr( 'id', 'thumb_preview_' + img.attr( 'id' ) ) );

			thumb_preview_img = $('#thumb_preview_' + img.attr( 'id' ));

			thumb_preview_img.css({position : 'relative', opacity : 1});
		}

		function init_thumbnail_box()
		{
			var thumb_box_width  = viewport.width();
			var thumb_box_height = viewport.height();
			var thumb_box_left   = 0;
			var thumb_box_top    = 0;

			if(options.crop_info)
			{
				thumb_box_width  = options.crop_info.thumb_box_width;
				thumb_box_height = options.crop_info.thumb_box_height;
				thumb_box_left   = options.crop_info.thumb_box_offset_x;
				thumb_box_top    = options.crop_info.thumb_box_offset_y;
			}
			
			thumb_box = $('#thumb_box_' + options.image_id);

			// for old images, reset thumbnail position properly if going out of bounds
			if (parseInt(thumb_box_width) + parseInt(thumb_box_left) > viewport.width())
				thumb_box_left = 0;
			if (parseInt(thumb_box_height) + parseInt(thumb_box_top) > viewport.height())
				thumb_box_top = 0;

			thumb_box.css({
				opacity    : 0.8,
				width      : thumb_box_width + 'px',
				height     : thumb_box_height + 'px',
				left       : thumb_box_left + 'px',
				top        : thumb_box_top + 'px',
				visibility : 'hidden'
			});

			thumb_box.draggable({
				containment : 'parent',
				drag        : function()
				{
					update_thumbnail_preview();
				}
			});

			thumb_box.resizable({
				aspectRatio : thumb_preview_box.width() / thumb_preview_box.height(),
				minWidth    : thumb_preview_box.width(),
				minHeight   : thumb_preview_box.height(),
				maxWidth    : viewport.width(),
				maxHeight   : viewport.height(),
				containment : 'parent',
				stop        : function()
				{
					update_thumbnail_preview();
				}
			});
		}

		function init_thumbnail_btns()
		{
			// setup the thumbbox toggle button clicks
			var toggle_btn = $( '#im_thumbbox_toggle_' + options.image_id );

			if( toggle_btn.length )
			{
				toggle_btn.click(function()
				{
					if( thumb_box.css( 'visibility' ) == 'visible' ) thumb_box.css( 'visibility', 'hidden' );
					else thumb_box.css( 'visibility', 'visible' );
				});
			}

			// setup the thumbbox maximize button click
			var maximize_btn = $( '#im_thumbbox_maximize_' + options.image_id );

			if( maximize_btn.length )
			{
				maximize_btn.click(function()
				{
					thumb_box.css({
						width      : viewport.width() + 'px',
						height     : viewport.height() + 'px',
						left       : '0px',
						top        : '0px',
						visibility : 'visible'
					});

					update_thumbnail_preview();
				});
			}
		}

		function update_thumbnail_preview()
		{
			// this the ratio of image dimension to the thumbbox dimension
			var ratio_imgwidth_tboxwidth   = img.width() / thumb_box.width();
			var ratio_imgheight_tboxheight = img.height() / thumb_box.height();

			// the calculated thumbbox preview image size which has the same proportions as the original image to the thumbbox
			var tpreview_img_width  = ratio_imgwidth_tboxwidth * thumb_preview_box.width();
			var tpreview_img_height = ratio_imgheight_tboxheight * thumb_preview_box.height();

			// how farther is the thumbbox left/top from image left/top
			var displacement_left = thumb_box.position().left - img.position().left;
			var displacement_top  = thumb_box.position().top - img.position().top;

			var tpreview_img_left = displacement_left * ( tpreview_img_width / img.width() );
			var tpreview_img_top  = displacement_top * ( tpreview_img_height / img.height() );

			thumb_preview_img.css({
				width    : tpreview_img_width + 'px',
				height   : tpreview_img_height + 'px',
				top      : -( tpreview_img_top ) + 'px',
				left     : -( tpreview_img_left ) + 'px'
			});

			save_crop_info();
		}

		function correct_img_position()
		{
			var img_top    = 0;
			var img_left   = 0;

			img.css({
				top  : img_top,
				left : img_left
			});
		}

		function save_crop_info()
		{
			// save the resized dimensions
			var crop_info = {
				// required to persist the image dimensions when it was saved
				// so that it can be loaded at the same state when the editor loads
				width              : img.width(),
				height             : img.height(),
				
				// the required info for large image generation
				zoom               : ( img.width() / img.data( 'original_width' ) ) * 100,
				large_width        : Math.min( img.width(), viewport.width() ),
				large_height       : Math.min( img.height(), viewport.height() ),
				offset_x           : Math.abs( img.position().left ),
				offset_y           : Math.abs( img.position().top ),

				// the required infor for thumbnail generation
				thumb_zoom         : ( thumb_preview_img.width() / img.data('original_width') ) * 100,
				thumb_width        : thumb_preview_box.width(),
				thumb_height       : thumb_preview_box.height(),
				thumb_offset_x     : Math.abs( thumb_preview_box.position().left - thumb_preview_img.position().left ),
				thumb_offset_y     : Math.abs( thumb_preview_box.position().top - thumb_preview_img.position().top ),
				
				thumb_box_width    : thumb_box.width(),
				thumb_box_height   : thumb_box.height(),
				thumb_box_offset_x : thumb_box.position().left,
				thumb_box_offset_y : thumb_box.position().top
			};

			img.data( 'im_crop_info', crop_info );
		}
	};

	$.image_resizer.get_crop_info = function(img_id)
	{
		var img = $( '#' + img_id );
		
		// if the image is not really an image then there are no resized dimensions, so return from the function
		if( ! img.is( 'img' ) )
		{
			return false;
		}

		return img.data( 'im_crop_info' );
	}

})(jQuery);