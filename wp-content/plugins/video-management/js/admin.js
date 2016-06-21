/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
(function($)
{

$( window ).load(function()
{
	// initialize the popup
	VM.popup.init();
	
	// initialize the video editor
	VM.editor.init();

	// initialize the gallery
	VM.gallery.init();
});

VM.popup = function()
{
	var popup;
	var content_area;
	var loading_img;

	return {
		init : function()
		{
			// setup the area where editor is loaded
			content_area = $( '#vm_popup_content' );

			// setup the popup
			popup = $( '#vm_popup' );

			popup.dialog({
				autoOpen   : false,
				width      : parseInt( popup.css( 'width' ) ),
				height     : parseInt( popup.css( 'height' ) ),
				modal      : true,
				draggable  : false,
				resizable  : false
			});

			// setup the loading image
			loading_img = $( '#vm_popup_loading_img' );
		},

		open : function(title)
		{
			popup.dialog( 'option', 'title', title );
			popup.dialog( 'open' );

			VM.popup.set_content('');
		},

		close : function()
		{
			VM.popup.set_content('');
			popup.dialog( 'close' );
		},

		set_content : function(content)
		{
			content_area.show().html( content );
		},

		show_loading : function()
		{
			$( '#vm_popup_wrap' ).fadeTo( 'fast', 0.2, function()
			{
				loading_img.show();
				loading_img.offset({
					top  : popup.offset().top + popup.height() / 2 - loading_img.height() / 2,
					left : popup.offset().left + popup.width() / 2 - loading_img.width() / 2
				});
			});
		},

		hide_loading : function()
		{
			$( '#vm_popup_wrap' ).fadeTo( 'medium', 1 );

			loading_img.hide();
		}
	}
}();

VM.editor = function()
{
	var post_id;
	var delete_img;
	var set_default_img;

	var make_manageable = function(item)
	{
		var $this = $( item );
		var video_id = $this.attr( 'id' ).match(/.*vm_gallery_item-([0-9]+).*/)[1];

		// make deleteable
		var delete_icon = delete_img.clone().removeAttr( 'id' ).addClass( 'vm_delete_icon' )
				.click(function()
				{
					VM.gallery.remove_item( video_id );
					return false;
				});

		$this
			.append( delete_icon )
			.hover(
					  function () {delete_icon.css('visibility', 'visible');},
					  function () {delete_icon.css('visibility', 'hidden');}
					);

		// make defaultable
		var set_default_icon = set_default_img.clone().removeAttr( 'id' ).addClass( 'vm_set_default_icon' )
				.click(function()
				{
					VM.gallery.set_active_item( video_id );
					return false;
				});

		$this
			.append( set_default_icon )
			.hover(
					  function () {set_default_icon.css('visibility', 'visible');},
					  function () {set_default_icon.css('visibility', 'hidden');}
					);
	}

	return {
		init : function()
		{
			post_id = $( '#vm_post_id' ).val();

			// setup the delete image
			delete_img = $( '#vm_delete_img' );

			// setup the set as default image
			set_default_img = $( '#vm_set_default_img' );

			// setup the add video click handler
			$( '#vm_add_video' ).click(function() {VM.editor.display_add_form();});

			// setup the edit click handlers
			$( '.vm_editable' ).click(function() {VM.editor.display_edit_form( this );});

			// setup the delete stuff
			$( '.vm_manageable' ).each(function() {make_manageable( this );});
		},
		
		display_edit_form : function( item )
		{
			var $item    = $( item );
			var video_id = $item.attr( 'id' ).match(/.*vm_gallery_item-([0-9]+).*/)[1];

			VM.popup.open( 'Edit Video' );
			VM.popup.show_loading();
			
			$.post(ajaxurl, {action : 'vm_edit_video_form', video_id : video_id}, function( data )
			{
				var content = $( data );
				content.find( '#vm_edit_btn' ).click(function()
				{
					VM.editor.save( video_id, $.trim( $( '#vm_title' ).val() ), $.trim( $( '#vm_caption' ).val() ) );
				});

				VM.popup.set_content( content );
				VM.popup.hide_loading();
			});
		},

		display_add_form : function()
		{
			VM.popup.open( 'Add Video' );
			VM.popup.show_loading();

			$.post(ajaxurl, {action : 'vm_add_video_form', post_id : post_id}, function( data )
			{
				var content = $( data );
				content.find( '#vm_add_btn' ).click(function() {VM.editor.add();});

				VM.popup.set_content( content );
				VM.popup.hide_loading();
			});
		},

		add : function()
		{
			var url      = $( '#vm_url' ).val();
			var add_msg  = $('#vm_add_video_msg');
			var save_btn = $( '#vm_add_btn' );

			save_btn.attr( 'disabled', 'disabled' );
			add_msg.attr( 'class', 'updated' ).show().find( 'p' ).html( 'Please wait while the video is being added ... ' );

			$.post(ajaxurl, {action : 'vm_add_video', post_id : post_id, video_url : url}, function(response)
			{
				// if there was any error during the file upload
				if( response.error )
				{
					add_msg.attr( 'class', 'error' ).show().find( 'p' ).html( response.error );
					save_btn.removeAttr( 'disabled' );
					
					return;
				}

				// display the success message
				add_msg.attr( 'class', 'updated' ).show().find( 'p' )
					.html( 'The video was added successfully. Please wait while the video editor loads.' );

				// add the video to the gallery and display the video editor
				// add to gallery
				$.post(ajaxurl, {action : 'vm_display_video', video_id : response.video_id}, function(data)
				{
					var item_html = $( data );

					// setup the click handler for this new gallery item
					item_html.click(function() {VM.editor.display_edit_form( item_html );});

					// setup the delete and set as default click handler
					make_manageable( item_html );

					// add to the gallery
					VM.gallery.add_item( response.video_id, item_html );

					// now display the image editor
					VM.editor.display_edit_form( item_html );
				});
			}, 'json');
		},

		save : function(video_id, title, caption)
		{
			var save_btn    = $( '#vm_edit_btn' );

			save_btn.attr( 'disabled', 'disabled' );
			VM.popup.show_loading();

			var payload = {
				action    : 'vm_save_video',
				video_id  : video_id,
				title     : title,
				caption   : caption
			};

			$.post(ajaxurl, payload, function(response)
			{
				// if the edited image was saved successfully
				if( response.error == null )
				{
					VM.gallery.update_item( response.video_id, response );

					VM.popup.hide_loading();
					VM.popup.close();
				}

			}, 'json');
		},
		
		set_default : function(item, video_id)
		{
			var $item = $( item );

			if( $item.hasClass( 'vm_active' ) )	return;

			var payload = {
				action   : 'vm_set_default_video',
				video_id : video_id,
				post_id  : post_id
			};

			$item.fadeTo( 'medium', 0.5 ).addClass( 'vm_active' );

			$.post(ajaxurl, payload, function(response)
			{
				// the image was set as active successfully
				if( response.error == null )
				{
					VM.gallery.set_active_item( video_id );
				}

				$item.fadeTo( 'fast', 1 ).removeClass( 'vm_active' );
			}, 'json');
		}
	}
}();

VM.gallery = function()
{
	var gallery;
	var post_id;

	var item_width       = 146;
	var upload_btn_width = 162;

	var setup_items_width = function()
	{
		var item_container = gallery.find( 'ul' );

		item_container.width( item_container.children().length * item_width );
	}

	return {
		init : function()
		{
			post_id = $( '#vm_post_id' ).val();
			
			gallery = $( '#vm_thumbnails ');

			// setup the gallery width
			gallery.find( '#vm_scrollable' ).width( gallery.width() - upload_btn_width ).show();
			setup_items_width();

			// initialize the tooltip
			gallery.find( 'img[title]' ).tooltip();
		},
		
		add_item : function(item_id, item_html)
		{
			// add the new item to the gallery
			var has_items = gallery.find( 'li' ).length;

			if( has_items ) // if there are items in the gallery, insert this one as the first one
			{
				gallery.find( 'li' ).first().before( item_html );
			}
			else // there are no items so make this the first one and make it the active one too
			{
				gallery.find( 'ul' ).append( item_html );
				VM.gallery.set_active_item( item_id );
			}

			// reset the items width because new item has been added
			setup_items_width();

			// setup the tooltip for the newly added item
			gallery.find( 'img[title]' ).first().tooltip();
		},

		update_item : function( item_id, details )
		{
			var item      = $( '#vm_gallery_item-' + item_id );
			var thumb     = item.find( 'img.thumb' );

			// update the tooltip
			var tooltip = thumb.data( 'tooltip' ).getTip() || thumb.data( 'tooltip' ).show().getTip();
			tooltip.html( details.title + '<br />' + details.caption ).hide();
		},

		remove_item : function( item_id )
		{
			if( false == confirm( 'Are you sure you want to delete this video?' ) ) return;
			
			var $item = $( '#vm_gallery_item-' + item_id );

			if( $item.hasClass( 'vm_active' ) )	return;

			$item.fadeTo( 'medium', 0.5 ).addClass( 'vm_active' );

			$.post(ajaxurl, {action : 'vm_delete_video', video_id : item_id, post_id : post_id}, function(response)
			{
				if(response.error == null)
				{
					$item.remove();

					// reset the items width because an item has been removed
					setup_items_width();
				}
			}, 'json');
		},
		
		set_active_item : function( item_id )
		{
			var $item = $( '#vm_gallery_item-' + item_id );

			if( $item.hasClass( 'vm_active' ) )	return;

			var payload = {
				action   : 'vm_set_default_video',
				video_id : item_id,
				post_id  : post_id
			};

			$item.fadeTo( 'medium', 0.5 ).addClass( 'vm_active' );

			$.post(ajaxurl, payload, function(response)
			{
				// the image was set as active successfully
				if( response.error == null )
				{
					gallery.find( 'li' ).removeClass( 'active' );
				}

				$item.fadeTo( 'fast', 1 ).removeClass( 'vm_active' ).addClass( 'active' );
			}, 'json');
		}
	}

}();

})(jQuery);