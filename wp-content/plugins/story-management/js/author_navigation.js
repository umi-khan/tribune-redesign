(function($)
{

$( document ).ready(function()
{
	SM_author_navigation.init();
});

SM_author_navigation = function()
{
	var author_navigation;
	var author_list_container;
	var loading_img;
	var ajax_url;

	var init_click_handlers = function()
	{
		author_navigation.find( 'a' ).click(function()
		{
			load_authors_list( this );

			return false;
		});
	}

	var load_authors_list = function(item)
	{
		var $item   = $( item );

		if( $item.hasClass( 'sm_author_link_active' ) ) return;

		var params  = [$.trim( $item.text() )];
		var payload = xml_json_rpc_helper.xmlize_request( 'sm_authors_get_list', params );

		$item.addClass( 'sm_author_link_active' );
		author_navigation.find( 'a' ).removeClass( 'active' );
		$item.addClass( 'active' );

		if( author_list_container.is( ':visible' ) ) author_list_container.fadeTo( 'fast', 0.3 );

		loading_img.show();

		$.ajax({
			url	      : ajax_url,
			type	      : 'POST',
			contentType : 'text/xml',
			processData : false,
			data	      : payload,
			dataType    : "xml",
			complete    : function(XMLHttpRequest)
			{
				var response = xml_json_rpc_helper.parse_response( XMLHttpRequest );

				$item.removeClass( 'sm_author_link_active' );

				if( response.error ) return

				author_list_container.html( response.html );

				author_list_container.fadeTo( 'fast', 1.0, function()
				{
					loading_img.hide();
					author_list_container.slideDown();
				});
			}
		});
	}

	return {
		init : function()
		{
			ajax_url              = SM_author_config.base_url + "/xmlrpc.php";

			author_navigation     = $( '#author-navigation .pagination' );
			author_list_container = $( '#authors #authors-list' );
			loading_img           = $( '#authors #loadin_img' );

			init_click_handlers();
		}
	}
}();

})(jQuery);