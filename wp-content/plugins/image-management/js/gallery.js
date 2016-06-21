/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($)
{

IM_gallery = function()
{
	var gallery;
	var cache;
	var delete_img;
	var set_default_img;

	var item_width       = 146;
	var upload_btn_width = 162;

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
		return ( gallery.length > 0 );
	}

	var update_cache = function()
	{
		gallery.find( 'li' ).each(function()
		{
			var $this = $( this );

			var item_id = $this.attr( 'id' ).match(/.*im_gallery_item-([0-9]+).*/)[1];
			var url = $.trim( $this.find( '#im_large_img_src-' + item_id ).text() );

			// the image has already been cached
			if(cache.indexOf( url ) != -1)	return;

			var img = new Image();

			// start loading the image
			img.src = url;

			// push the url in the cache
			cache.push( url );
		});
	}

	var make_deleteable = function(item_id)
	{
		var $this = $( '#im_gallery_item-' + item_id );
		var delete_icon = delete_img.clone()
				.attr( 'id', 'delete_img_' + item_id )
				.addClass( 'im_delete_icon' )
				.click(function()
				{
					IM_gallery.remove_item( item_id );
					return false;
				});

		$this
			.append( delete_icon )
			.hover(
					  function () {delete_icon.css('visibility', 'visible');},
					  function () {delete_icon.css('visibility', 'hidden');}
					);
	}

	var make_defaultable = function(item_id)
	{
		var $this = $( '#im_gallery_item-' + item_id );
		var set_default_icon = set_default_img.clone()
				.attr( 'id', 'set_default_img_' + item_id )
				.addClass( 'im_set_default_icon' )
				.click(function()
				{
					IM_gallery.set_active_item( item_id );
					return false;
				});

		$this
			.append( set_default_icon )
			.hover(
					  function () {set_default_icon.css('visibility', 'visible');},
					  function () {set_default_icon.css('visibility', 'hidden');}
					);
	}

	var get_post_id_from_class = function(item)
	{
		var $item    = $( item );
		var classes = $item.attr('class').split(' ');

		for(var i=0; i < classes.length; i++)
		{
			if(classes[i].indexOf('im_post_id-') != -1) return classes[i].match(/.*im_post_id-([0-9]+).*/)[1];
		}
	}

	var setup_items_width = function()
	{
		var item_container = gallery.find( 'ul' );

		item_container.width( item_container.children().length * item_width );
	}

	return {
		init : function()
		{
			observers = new Array();

			gallery = $( '#im_thumbnails ');

			// initialize the cache
			cache = new Array();

			// setup the gallery width
			gallery.find( '#im_scrollable' ).width( gallery.width() - upload_btn_width ).show();
			setup_items_width();

			// initialize the tooltip
			gallery.find( 'img[title]' ).tooltip();

			// cache all the large images
			update_cache();

			// setup the delete image
			delete_img = $( '#im_delete_img' );

			// setup the set as default image
			set_default_img = $( '#im_set_default_img' );

			$( '.im_manageable' ).each( function()
			{
				var $item   = $( this );
				var item_id = $item.attr( 'id' ).match(/.*im_gallery_item-([0-9]+).*/)[1];

				make_deleteable( item_id );
				make_defaultable( item_id );
			});
		},
		
		add_item : function(item_id)
		{
			$.post(ajaxurl, {action : 'im_display_image', image_id : item_id}, function(data)
			{
				var item_html = $( data );

				// add the new item to the gallery
				var has_items = gallery.find( 'li' ).length;

				if( has_items ) // if there are items in the gallery, insert this one as the first one
				{
					gallery.find( 'li' ).first().before( item_html );
				}
				else // there are no items so make this the first one and make it the active one too
				{
					gallery.find( 'ul' ).append( item_html );
					IM_gallery.set_active_item( item_id );
				}

				// reset the items width because new item has been added
				setup_items_width();

				// setup the tooltip for the newly added item
				gallery.find( 'img[title]' ).first().tooltip();

				// update the cache so that newly added image is also cached
				update_cache();

				// make the new item deleteable and defaultable
				make_deleteable( item_id );
				make_defaultable( item_id );

				notify( 'add', item_id );
			});
		},
		
		update_item : function( item_id )
		{
			var item = $( '#im_gallery_item-' + item_id );
			
			if( item.length < 1 ) return;

			$.post(ajaxurl, {action : 'im_display_image', image_id : item_id}, function(data)
			{
				var item_html     = $( data );
				var thumb_new     = item_html.find( '.thumb' );
				var large_src_new = item_html.find( '#im_large_img_src-' + item_id ).text();

				var thumb     = item.find( 'img.thumb' );
				var large_img = item.find( '#im_large_img_src-' + item_id );

				thumb.attr({src : thumb_new.attr( 'src' )});

				// update the tooltip
				var tooltip = thumb.data( 'tooltip' ).getTip() || thumb.data( 'tooltip' ).show().getTip();
				tooltip.html( thumb_new.attr( 'title' ) ).hide();

				// we need to append query string to the large image url so that a new one is fetched by the browser
				large_img.text( large_src_new );

				update_cache();

				notify( 'update', item_id );
			});
		},

		remove_item : function( item_id )
		{
			if( false == confirm( 'Are you sure you want to delete this image?' ) ) return;

			var $item = $( '#im_gallery_item-' + item_id );

			if( $item.hasClass( 'im_active' ) )	return;

			$item.fadeTo( 'medium', 0.5 ).addClass( 'im_active' );

			var post_id = get_post_id_from_class( $item );

			if( false == post_id ) return;

			$.post(ajaxurl, {action : 'im_delete_image', image_id : item_id, post_id : post_id}, function(response)
			{
				if(response.error == null)
				{
					var is_active = IM_gallery.is_active_item( item_id );

					$item.remove();

					// if this was the default item, make the first one the default one
					if( is_active ) IM_gallery.set_active_item( IM_gallery.get_first_item() );

					// reset the items width because an item has been removed
					setup_items_width();

					notify( 'remove', item_id );
				}
			}, 'json');
		},

		set_active_item : function( item_id )
		{
			var $item = $( '#im_gallery_item-' + item_id );
			
			if( $item.hasClass( 'im_active' ) )	return;

			var post_id = get_post_id_from_class( $item );
			
			if( false == post_id ) return;

			var payload = {
				action   : 'im_set_default_image',
				image_id : item_id,
				post_id  : post_id
			};

			$item.fadeTo( 'medium', 0.5 ).addClass( 'im_active' );

			$.post(ajaxurl, payload, function(response)
			{
				$item.fadeTo( 'fast', 1 ).removeClass( 'im_active' );

				// the image was set as active successfully
				if( response.error == null )
				{
					gallery.find( 'li' ).removeClass( 'active' );
					$( '#im_gallery_item-' + item_id ).addClass( 'active' );
				}
			}, 'json');
		},

		is_active_item : function( item_id )
		{
			return $( '#im_gallery_item-' + item_id ).hasClass( 'active' );
		},
		
		get_first_item  : function()
		{
			// add the new item to the gallery
			var has_items = gallery.find( 'li' ).length;
			var item_id   = 0;

			if( has_items ) // if there are items in the gallery, insert this one as the first one
			{
				item_id = gallery.find( 'li' ).first().attr( 'id' ).match(/.*im_gallery_item-([0-9]+).*/)[1];
			}

			return item_id;
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
					IM_gallery.add_item( item_id );
					return;

				case 'update':
					IM_gallery.update_item( item_id );
					return;

				case 'remove':
					return;
			}
		}
	}

}();

})(jQuery);