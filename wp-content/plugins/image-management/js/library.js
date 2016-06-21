/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($)
{

IM_library = function()
{
	var search_form;
	var post_id;

	var observers;
	var notify = function(action, item_id)
	{
		for( var i=0; i < observers.length; i++ )
		{
			if( typeof observers[i].do_action == 'function' ) observers[i].do_action( action, item_id );
		}
	}

	var is_visible = function()
	{
		return ( $( '#im_media_library' ).length > 0 );
	}
	
	var init_click_handlers = function()
	{
		$( '#im_media_library a.page-numbers' ).click(function()
		{
			var page_num = $( this ).attr( 'href' ).match(/.*paged=([0-9]+).*/)[1];

			search_form.find( 'input[name="paged"]' ).val( page_num );
			search_form.submit();

			return false;
		});

		$( '.add_library_item' ).click(function()
		{			
			IM_library.add_to_post( this );
		});

		$( '.edit_library_item' ).click(function()
		{
			var $this = $( this );
			var image_id = $this.parents( '.im_library_item' ).attr( 'id' ).match(/.*im_library_item-([0-9]+).*/)[1];
			
			IM_editor.open( image_id );
		});

		$( '.delete_library_item' ).click(function()
		{
			var $this = $( this );
			var image_id = $this.parents( '.im_library_item' ).attr( 'id' ).match(/.*im_library_item-([0-9]+).*/)[1];
			
			IM_library.remove_item( image_id );
		});
	}

	var init_form_submit_handler = function()
	{
		search_form.submit( function()
		{
			IM_popup.show_loading();

			$.get(ajaxurl, search_form.serialize(), function(response)
			{
				IM_popup.set_content( response );

				search_form = $( '#im_media_search_form' );

				init_click_handlers();
				init_form_submit_handler();
				attach_uploader();

				IM_popup.hide_loading();
			});

			return false;
		});
	}

	var attach_uploader = function()
	{
		if( $( '#im_upload_media_btn' ).length < 1 ) return;

		var upload_msg = $('#im_upload_msg');

		new AjaxUpload('im_upload_media_btn',
		{
			action       : ajaxurl,
			name         : 'im_image_file',
			data         : {action  : 'im_library_upload_image', post_id : post_id},
			autoSubmit   : true,
			responseType : 'json',
			onSubmit     : function(file, extension)
			{
				upload_msg.hide();

				if (extension && /^(jpg|png|jpeg|gif)$/.test(extension))
				{
					$( '#im_upload_media_btn' ).attr( 'disabled', 'disabled' );

					upload_msg.attr( 'class', 'updated' ).show().find( 'p' )
						.html( 'Please wait while the file is being uploaded ... ' );

					IM_popup.show_loading();
				}
				else
				{
					upload_msg.attr( 'class', 'error' ).show().find( 'p' ).html( 'Error: Only images are allowed' );
					// cancel upload
					return false;
				}
			},

			onComplete : function(file, response)
			{
				// if there was any error during the file upload
				if( response.error )
				{
					upload_msg.attr( 'class', 'error' ).show().find( 'p' ).html( response.error );
					IM_popup.hide_loading();
					return;
				}

				window.setTimeout(function()
				{
					 jQuery('#im_upload_msg').hide();
				}, 500);

				// display the success message
				upload_msg.attr( 'class', 'updated' ).show().find( 'p' )
					.html( 'The image was uploaded successfully. Please wait while the image editor loads.' );

				$( '#im_upload_media_btn' ).removeAttr( 'disabled' );

				IM_editor.open( response.image_id );

				// notify the observers that a new image has been added to the post
				notify( 'add', response.image_id );

				if( is_visible() ) IM_library.add_item( response.image_id );
			}
		});
	}

	var get_post_id_from_class = function(item)
	{
		var $item   = $( item );
		var post_id = 0;

		if( $item.length )
		{
			var classes = $item.attr('class').split(' ');

			for(var i=0; i < classes.length; i++)
			{
				if(classes[i].indexOf('im_post_id-') != -1) post_id = classes[i].match(/.*im_post_id-([0-9]+).*/)[1];
			}
		}

		return post_id;
	}

	return {		
		init : function(current_post_id)
		{
			observers = new Array();
			
			post_id     = current_post_id;
			
			search_form = $( '#im_media_search_form' );

			init_click_handlers();
			attach_uploader();
		},

		open : function()
		{
			IM_popup.open( 'Media Library' );
			IM_popup.show_loading();

			$.post(ajaxurl, {action : 'im_display_media_library', post_id : post_id}, function( response )
			{
				IM_popup.set_content( response );

				search_form = $( '#im_media_search_form' );

				init_click_handlers();
				init_form_submit_handler();
				attach_uploader();

				IM_popup.hide_loading();
			});
		},

		add_to_post : function(btn)
		{
			var $btn    = $(btn);
			var $item   = $btn.parents( '.im_library_item' );
			var item_id = $item.attr( 'id' ).match(/.*im_library_item-([0-9]+).*/)[1];

			IM_popup.show_loading();

			var payload = {
				action   : 'im_library_add_image',
				post_id  : post_id,
				image_id : item_id
			};

			$.post(ajaxurl, payload, function(response)
			{
				// if any error, show it and exit
				if( response.error )
				{
					$btn.before( '<span class="error">' + response.error + '</span>' );
					IM_popup.hide_loading();

					return;
				}

				// notify the observers that a new image has been added to the post
				notify( 'add', response.image_id );

				IM_editor.open( response.image_id );
			}, 'json');
		},

		add_item : function(image_id)
		{
			$.post(ajaxurl, {action : 'im_library_display_image', image_id : image_id}, function(data)
			{
				var $item = $( data );
				$item.find( '.edit_library_item' ).click(function(){IM_editor.open( image_id );});
				$item.find( '.delete_library_item' ).click(function(){IM_library.remove_item( image_id );});

				var items = $( '.im_library_item' );
				
				if( items.length > 0 ) $( '.im_library_item' ).first().before( $item );
				else                   $( '#im_library_items' ).append( $item );
			});
		},

		remove_item : function( item_id )
		{
			if( false == confirm( 'Are you sure you want to delete this image?' ) ) return;

			var $item = $( '#im_library_item-' + item_id );

			if( $item.hasClass( 'im_active' ) )	return;

			$item.fadeTo( 'medium', 0.5 ).addClass( 'im_active' );

			var item_post_id = get_post_id_from_class( $item );

			$.post(ajaxurl, {action : 'im_delete_image', image_id : item_id, post_id : item_post_id}, function(response)
			{
				if(response.error == null)
				{
					$item.empty();
					window.location.reload( true );
				}
			}, 'json');
		},

		add_observer : function(obj)
		{
			observers.push( obj );
		},

		do_action : function(action, item_id)
		{
			if( false == is_visible() ) return;
			
			switch( action )
			{
				case 'add':
					return;

				case 'update':
					window.location.reload( true );
					return;

				case 'remove':
					return;
			}
		}
	}
}();
	
})(jQuery);