/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($)
{

IM_editor = function()
{
	var observers;
	var notify = function(action, item_id)
	{
		for( var i=0; i < observers.length; i++ )
		{
			if( typeof observers[i].do_action == 'function' ) observers[i].do_action( action, item_id );
		}
	}
	
	return {
		init : function()
		{
			observers = new Array();
			
			// setup the edit stuff
			$( '.im_editable' ).each( function()
			{
				var $item    = $( this );
				var image_id = $item.attr( 'id' ).match(/.*im_gallery_item-([0-9]+).*/)[1];

				IM_editor.make_editable( image_id );
			});
		},

		open : function( image_id )
		{
			IM_popup.open( 'Edit Image' );
			IM_popup.show_loading();

			$.post(ajaxurl, {action : 'im_display_editor', image_id : image_id}, function( response )
			{
				IM_popup.set_content( response );
			});
		},

		load_image : function(img_id, img_html_id, img_src, editor_options)
		{
			var img_to_edit = $( '#' + img_html_id );
			var preload_img = new Image();

			img_to_edit.css({opacity : 0});
			
			preload_img.onload = function()
			{
				img_to_edit.attr( 'src', img_src );
				
				$.image_resizer.resize( editor_options );

				IM_editor.auto_save( img_id, img_html_id );
				
				img_to_edit.css({opacity : 1});
				
				$( '#im_save_' + img_id ).click(function()
				{
					IM_editor.save( img_id, img_html_id );
				});
				
				IM_popup.hide_loading();
			};

			preload_img.src = img_src;
		},

		auto_save : function(img_id, img_html_id)
		{
			var crop_info = $.image_resizer.get_crop_info(img_html_id);

			if( false == crop_info || $( '#' + img_html_id ).length < 1 ) return;

			var payload = {
				action    : 'im_save_image',
				image_id  : img_id,
				title     : $('#im_title').val(),
				caption   : $('#im_caption').val(),
				crop_info : crop_info
			};

			$.post(ajaxurl, payload, function(response)
			{
				// if the edited image was saved successfully
				if( response.error == null )
				{
					// notify the observers that a new image has been added to the post
					notify( 'update', response.image_id );
				}

			}, 'json');
		},

		save : function(img_id, img_html_id)
		{
			var crop_info = $.image_resizer.get_crop_info(img_html_id);

			if( false == crop_info || $( '#' + img_html_id ).length < 1 ) return;

			var save_btn  = $( '#im_save_' + img_html_id );

			save_btn.attr( 'disabled', 'disabled' );			
			IM_popup.show_loading();
			
			var payload = {
				action    : 'im_save_image',
				image_id  : img_id,
				title     : $('#im_title').val(),
				caption   : $('#im_caption').val(),
				crop_info : crop_info
			};

			$.post(ajaxurl, payload, function(response)
			{
				// if the edited image was saved successfully
				if( response.error == null )
				{
					// notify the observers that a new image has been added to the post
					notify( 'update', response.image_id );

					IM_popup.hide_loading();
					IM_popup.close();
				}

			}, 'json');
		},

		make_editable : function(image_id)
		{
			var $this = $( '#im_gallery_item-' + image_id );

			if( $this.length > 0 ) $this.click( function() {IM_editor.open( image_id );} );
		},
		
		attach_editor : function(settings)
		{
			$.image_resizer.resize(settings);
		},

		add_observer : function(obj)
		{
			observers.push( obj );
		},

		do_action : function(action, item_id)
		{
			switch( action )
			{
				case 'add':
					IM_editor.make_editable( item_id );
					return;

				case 'update':
					return;

				case 'remove':
					return;
			}
		}
	}
}();

})(jQuery);