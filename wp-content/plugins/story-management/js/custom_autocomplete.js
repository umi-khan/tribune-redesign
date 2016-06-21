(function($)
{

SM_autocomplete = function()
{
	var container;

	var hint_content;
	var hint_text;
	var hint_color;

	var loading_img;
	var warning_img;

	var post_id;
	var remote_add_action;
	var remote_remove_action;
	var remote_url;

	var autocomplete_field;
	var new_item_content;
	var selected_items_container;
	var add_item_btn;
	var delete_item_link;

	var item_content_id_prefix;

	var selected_items;
	var items;
	var in_process_items;

	/**
	 * var options = {
			items                  : new Array(),
			selected_items         : new Array(),
			container              : '#items_container',
			item_content_id_prefix : 'selected_item-',
			remote_url             : ajaxurl,
			remote_add_action      : 'add_item',
			remote_remove_action   : 'remove_item',
			post_id                : 0
		};
	 */
	this.init = function(options)
	{		
		selected_items           = new Array();
		items                    = options.items;
		in_process_items         = new Array();

		post_id                  = options.post_id;
		remote_add_action        = options.remote_add_action;
		remote_remove_action     = options.remote_remove_action;
		remote_url               = options.remote_url;
		
		container                = $( options.container );

		autocomplete_field       = container.find( '#autocomplete_field' );
		new_item_content         = container.find( '#new_item_content' );
		selected_items_container = container.find( '#selected_items' );
		add_item_btn             = container.find( '#add_item_btn' );
		hint_content             = container.find( '#add_item_hint' );
		delete_item_link         = container.find( '.item_del_btn' );

		loading_img              = container.find( '#loading_img' );
		warning_img              = container.find( '#warning_img' );
		
		item_content_id_prefix   = options.item_content_id_prefix;

		init_event_handlers();

		init_hint();

		show_hint();

		for( var i in options.selected_items ) add_item_local( options.selected_items[i] );
	}

	var init_event_handlers = function()
	{
		autocomplete_field.focus(function()
		{
			if( autocomplete_field.val() == hint_text ) hide_hint();
		});

		autocomplete_field.blur(function()
		{
			if( $.trim( autocomplete_field.val() ).length < 1 ) show_hint();
		});

		autocomplete_field.autocomplete( items,
		{
			matchContains : true,
			formatItem    : function(item)
			{
				return item.name;
			}
		});

		autocomplete_field.keypress(function(event)
		{
			if( event.which != 13 ) return;

			var item_name = autocomplete_field.val();

			if( item_name == hint_text ) return;
			
			add_item( item_name );
			autocomplete_field.val( '' );
			
			event.preventDefault();
		});

		add_item_btn.click(function()
		{
			var item_name = autocomplete_field.val();

			if( item_name == hint_text ) return;

			add_item( item_name );
			autocomplete_field.val( '' );
		});

		delete_item_link.live( 'click', function()
		{
			var index = $( this ).parent().attr( 'id' ).substr( item_content_id_prefix.length );
			remove_item( index );

			return false;
		});
	}
	
	var add_item = function(item_name)
	{
		if( item_name.length < 1 ) return;
		
		var item = find_item_by_name( item_name );

		if( has_item( item, selected_items ) || has_item( item, in_process_items ) ) return;

		var is_new_item = ( false == item );
		if( is_new_item ) item = {id : item_name, name : item_name};

		in_process_items.push( item );

		warning_img.hide();
		loading_img.show();

		$.ajax({
			type     : 'POST',
			url      : remote_url,
			data     : {action : remote_add_action, sm_post_id : post_id, sm_item : item.id},
			dataType : 'json',
			success  : function(response)
			{
				loading_img.hide();

				if( response.error )
				{
					warning_img.show().attr( 'title', response.error );
					return;
				}

				add_item_local( response.item );
				remove_in_process_item( item );
			}
		});
	}

	var remove_item = function(index)
	{
		var item = selected_items[index];
		
		if( has_item( item, in_process_items ) ) return;

		in_process_items.push( item );
		
		loading_img.show();
		warning_img.hide();

		$.ajax({
			type     : 'POST',
			url      : remote_url,
			data     : {action : remote_remove_action, sm_post_id : post_id, sm_item : item.id},
			dataType : 'json',
			success  : function(response)
			{
				loading_img.hide();

				if( response.error )
				{
					warning_img.show().attr( 'title', response.error );
					return;
				}

				remove_item_local( response.item );
				remove_in_process_item( item );
			}
		});
	}

	var add_item_local = function(item)
	{
		if( false == item ) return;

		var new_item = new_item_content.clone();
		new_item.attr( 'id', item_content_id_prefix + selected_items.length );
		new_item.html( new_item.html() + '&nbsp;' + item.name );

		selected_items_container.append( new_item );

		selected_items.push( item );
	}

	var remove_item_local = function(item)
	{
		if( false == item ) return;
		
		var index = find_selected_item_index( item.id );
		
		$( '#' + item_content_id_prefix + index ).remove();

		delete selected_items[index];
	}

	var remove_in_process_item = function(item)
	{
		for( var i in in_process_items )
		{
			if( in_process_items[i].id == item.id && in_process_items[i].name == item.name )
			{
				delete in_process_items[i];
				return;
			}
		}
	}

	var find_item_by_name = function(item_name)
	{
		for( var i in items )
		{
			if( items[i].name == item_name ) return items[i];
		}

		return false;
	}

	var has_item = function(item, items_array)
	{
		for( var i in items_array )
		{
			if( items_array[i].id == item.id && items_array[i].name == item.name ) return true;
		}

		return false;
	}

	var find_selected_item_index = function(item_id)
	{
		for( var i in selected_items )
		{
			if( selected_items[i].id == item_id ) return i;
		}

		return false;
	}

	var init_hint = function()
	{
		hint_text  = hint_content.text();
		hint_color = hint_content.css( 'color' );

		autocomplete_field.data( 'old_color', autocomplete_field.css( 'color' ) );
	}

	var show_hint = function()
	{
		autocomplete_field.val( hint_text );
		autocomplete_field.css( 'color', hint_color );
	}

	var hide_hint = function()
	{
		autocomplete_field.val( '' );
		autocomplete_field.css( 'color', autocomplete_field.data( 'old_color' ) );
	}
}

})(jQuery);