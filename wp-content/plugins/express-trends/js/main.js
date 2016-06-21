(function($)
{

$( document ).ready(function()
{
	ET_stories.init();
});

var ET_stories = function()
{
	var $more_pager_container;
	var $more_pager;
	var $spinner_img;
	var records_offset;
	var trend_slug;

	var get_data_from_class = function()
	{
		var classes = $more_pager.attr( 'class' ).split( ' ' );

		for( var i=0; i < classes.length; i++ )
		{
			if( classes[i].indexOf( 'offset-' ) != -1 ) records_offset = classes[i].replace( 'offset-', '' );

			if( classes[i].indexOf( 'slug-' ) != -1 ) trend_slug = classes[i].replace( 'slug-', '' );
		}

		return true;
	}

	var load_more_stories = function()
	{
		// if the button is already clicked and the stories are loading then prevent the button from being clicked again
		if( $more_pager.hasClass( 'trending_active' ) ) return;

		var params  = { slug : trend_slug, offset : records_offset };
		var payload = xml_json_rpc_helper.xmlize_request( 'et_more_stories', params );

		$more_pager.css( 'background', 'none' ).addClass( 'trending_active' );
		$more_pager.html( $spinner_img.clone().show().removeAttr( 'id' ) );

		$.ajax({
			url	      : ET_config.ajax_url,
			type	      : 'POST',
			contentType : 'text/xml',
			processData : false,
			data	      : payload,
			dataType    : 'html',
			success     : function(response)
			{
				// append the new stories
				append_more_stories( response );

				// remove the old more stories button
				$more_pager_container.remove();

				// reinitialize
				ET_stories.init();
			}
		});
	}

	var append_more_stories = function(html)
	{
		var $response = $( html );

		// if there is an empty response do nothing
		if( $response.length < 1 ) return;

		// create the twitter buttons
		/*$response.find('a.twitter-share-button').each(function()
		{
			var tweet_button = new twttr.TweetButton( $( this ).get( 0 ) );
			tweet_button.render();
		});*/

		// create the facebook buttons
		$response.find('.fb-link').each(function()
		{
			FB.XFBML.parse( this );
		});

		// append the new response to the document body
		$more_pager_container.before( $response );
		
		$.ajax({ url: 'http://platform.twitter.com/widgets.js', dataType: 'script', cache:true});
	}

	return {
		init: function()
		{
			$more_pager = $( '#trending_more_pager' );

			var has_more_stories = $more_pager.length > 0;
			if( false == has_more_stories ) return;

			$more_pager_container = $( '#trend_more_pager_anchor' );
			$spinner_img          = $( '#trending_spinner' );

			get_data_from_class();

			$more_pager.click( function()
			{
				load_more_stories();

				return false;
			});
		}
	}
}();

})(jQuery);