(function($)
{

$( document ).ready(function()
{
	ERP_search.init();
});

ERP_search = function()
{
	var $search_results_container;
	var $search_results;
	
	var $selected_posts_container;
	var $save_selections_btn;
	var $save_selections_msg;
	var $no_selections_text;

	var $search_btn;
	var $search_field;
	var $duration_field;
	var $post_id_field;

	var $loading_search_img;
	var $loading_saving_img;

	var do_search = function(search_text, page_num)
	{
		$loading_search_img.show();
		$search_results_container.css( 'opacity', 0.5 );
		$search_btn.attr( 'disabled', true );

		var payload = {
			action          : 'erp_do_search',
			erp_search_text : search_text,
			erp_duration    : $duration_field.val(),
			erp_post_id     : $post_id_field.val(),
			erp_page_num    : page_num
		};

		$.ajax({
			dataType : 'html',
			data     : payload,
			type     : 'POST',
			url      : ajaxurl,
			success  : function(response)
			{
				$search_results_container.html( response );
				$search_results_container.css( 'opacity', 1.0 );
				
				$search_results = $search_results_container.find( 'ul' );

				setup_pagination_controls();

				$loading_search_img.hide();
				$search_btn.removeAttr( 'disabled' );
			}
		});
	}

	var setup_pagination_controls = function()
	{
		// pagination button click handler
		$search_results_container
			.find( '.erp_pagination' )
			.find( 'a' )
			.click(function()
			{
				var search_text = $.trim( $search_field.val() );

				if( search_text.length < 1 ) return false;

				var $item    = $( this );
				var page_num = $item.attr( 'id' ).match(/.*erp_pagenum-([0-9]+).*/)[1];

				do_search( search_text, page_num );

				return false;
			});
	}

	var save_selections = function()
	{
		var selected_list = [];

		$selected_posts_container.children( 'li' ).each(function()
		{
			selected_list.push( $( this ).children( '.erp_del_post_link' ).attr( 'id' ).match(/.*erppostid-([0-9]+).*/)[1] );
		});

		if( selected_list.length < 1 ) return;

		$loading_saving_img.show();
		$save_selections_btn.attr( 'disabled', true );
		$save_selections_msg.html('');

		var payload = {
			action            : 'erp_save_selections',
			erp_related_posts : selected_list.toString(),
			erp_post_id       : $post_id_field.val()
		};

		$.ajax({
			dataType : 'json',
			data     : payload,
			type     : 'POST',
			url      : ajaxurl,
			success  : function(response)
			{
				var msg       = 'Related posts saved successfully!';
				var msg_class = 'erp_success';
				
				if( response.error )
				{
					msg       = response.error;
					msg_class = 'erp_error';
				}

				$loading_saving_img.hide();
				$save_selections_msg.html( msg ).attr( 'class', msg_class );
				$save_selections_btn.removeAttr( 'disabled' );
			}
		});
	}

	var add_to_search_results = function(item)
	{
		if( null == $search_results || $search_results.length < 1 ) return;

		var item_html = '<li id="' + item.find( 'a.erp_del_post_link' ).attr( 'id' ) + '">';
		item_html    += '<span class="erp_post_title">';
		item_html    += item.find( 'span.erp_post_title' ).html();
		item_html    += '</span> <br />';
		item_html    += '<span class="erp_date">';
		item_html    += item.find( 'span.erp_date' ).html();
		item_html    += '</span> | ';
		item_html    += '<a class="erp_add_post_link" href="' + item.find( 'a.erp_post_link' ).attr( 'href' ) + '" target="_blank">';
		item_html    += item.find( 'a.erp_post_link' ).html();
		item_html    += '</a>';
		item_html    += '</li>';

		$search_results.append( item_html );
	}

	var add_to_selections = function(item)
	{
		var $item        = $( item );
		var post_id      = $item.attr( 'id' ).match(/.*erppostid-([0-9]+).*/)[1];
		var post_details = $item.html();

		if( $selected_posts_container.find( '#erppostid-' + post_id ).length > 0 ) return;

		var new_item_html = '<li>';
		new_item_html    += '<a class="erp_del_post_link" id="erppostid-' + post_id + '" href="#"></a>';
		new_item_html    += post_details;
		new_item_html    += '</li>';

		$selected_posts_container.append( new_item_html );

		$item.remove();

		$no_selections_text.hide();

		if( $search_results.children().length < 1 ) $search_results_container.html( '<p>No more search results remaining!' );
	}

	var remove_from_selections = function(item)
	{
		var $item  = $( item );
		var parent = $item.parents( 'li' );

		add_to_search_results( parent );

		parent.remove();

		if( $selected_posts_container.children().length < 1 ) $no_selections_text.show();
	}

	return {
		init : function()
		{
			$search_results_container = $( '#erp_search_results' );
			$selected_posts_container = $( '#erp_selections > ul' );

			$no_selections_text       = $( '#erp_selected_posts > p' );

			$search_btn               = $( '#erp_search_posts_btn' );
			$search_field             = $( '#erp_search_field' );
			$duration_field           = $( '#erp_duration' );
			$post_id_field            = $( '#erp_post_id' );

			$save_selections_btn      = $( '#erp_save_posts_btn' );
			$save_selections_msg      = $( '#erp_save_selections_msg' );

			$loading_search_img       = $( '#erp_loading_search_img' );
			$loading_saving_img       = $( '#erp_loading_saving_img' );

			// click handler for the search posts button
			$search_btn.click(function()
			{
				var search_text = $.trim( $search_field.val() );

				if( search_text.length < 1 ) return;

				do_search( search_text, 0 );
			});

			// enter key handler for the search posts query string field
			$search_field.keypress(function(event)
			{
				if( event.which != 13 ) return;

				var search_text = $.trim( $( this ).val() );

				if( search_text.length < 1 ) return;

				do_search( search_text, 0 );

				event.preventDefault();
			});

			// click handler for saving the user related post selections
			$save_selections_btn.click(function()
			{
				save_selections();
			});

			// click handler for the adding post to selected posts list
			$search_results_container.find( 'li' ) .live( 'click', function(e)
			{
				if( $( e.target ).hasClass( 'erp_post_link' ) ) return;

				add_to_selections( this );
			});

			// click handler for removing post from selected posts list
			$( '.erp_del_post_link' ).live( 'click', function()
			{
				remove_from_selections( this );

				return false;
			});

			// make sortable
			$selected_posts_container.sortable({
				items       : 'li',
				cursor      : 'move',
				containment : 'parent'
			});
		}
	}
}();

})(jQuery);