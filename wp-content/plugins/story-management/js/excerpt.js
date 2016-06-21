(function($)
{

$( document ).ready( function()
{
	SM_excerpt.init( SM_story_config.excerpt_max_length );

});



SM_excerpt = function()
{
	var excerpt_box;
	var error_content;
	var error_content_tags;
	var post_status;
	var chars_limit;
	var validexcerpt = 0;
	var validtags = 0;

	var validate_field = function()
	{
		if( post_status != 'publish' ) return true;

		
		if ( $('#tagsdiv-post_tag').length > 0 ){

				//Check if tag box exist 
				if( ( excerpt_box.val().length < 1 || excerpt_box.val().length > chars_limit )  ||  ( $('#post_tag .tagchecklist span').length ==0 ) )
					{
						if( excerpt_box.val().length < 1 || excerpt_box.val().length > chars_limit )
							{
								validexcerpt = 1;
							}
						else
							{
								validexcerpt = 0;
								error_content.hide();
							}

						if( $('#post_tag .tagchecklist span').length == 0 )
							{
								validtags = 1;
							}
						else
							{
								validtags = 0;
								error_content_tags.hide();
							}
						return false;	
					}


			} else {

				//Check if Tag box NOt exist
						if( ( excerpt_box.val().length < 1 || excerpt_box.val().length > chars_limit ) )
					{
						if( excerpt_box.val().length < 1 || excerpt_box.val().length > chars_limit )
							{
								validexcerpt = 1;
							}
						else
							{
								validexcerpt = 0;
								error_content.hide();
							}

							//Pass tags value
								validtags = 0;
								error_content_tags.hide();
					 
						return false;	
					}

			}

 
		excerpt_box.removeClass( 'sm_excerpt_error' );
		$('[id^="tagsdiv-post_tag"]').css({'background':'#FFFFFF', 'border':'#d0d0cb solid 1px'});
		validexcerpt = 0;
		validtags = 0;
		error_content.hide();
		error_content_tags.hide();
		
		return true;
	}

	var show_error = function()
	{


		excerpt_box.removeClass( 'sm_excerpt_error' );
		$('[id^="tagsdiv-post_tag"]').css({'background':'#FFFFFF', 'border':'#d0d0cb solid 1px'});


		if (validexcerpt == 1){
			$( '#postexcerpt' ).spotlight();
			excerpt_box.addClass( 'sm_excerpt_error' ).focus();
			var error_txt;

			if( excerpt_box.val().length == 0 )
				error_txt = 'Excerpt must be provided';
			else if( excerpt_box.val().length < chars_limit )
				error_txt = 'There should be at least ' + chars_limit + ' characters in the excerpt';
			else
				error_txt = 'There can be no more than ' + chars_limit + ' characters in the excerpt';

			error_content.text( error_txt ).show();
		}

		if (validtags == 1){
			$( '#tagsdiv-post_tag' ).spotlight();
			$('[id^="tagsdiv-post_tag"]').css({'border':'#CC0000 solid 1px'});
			error_txt_tags = ' Tags must be provided.';
			error_content_tags.text( error_txt_tags ).show();
		}

	}

	return {
		init : function(limit)
		{
			excerpt_box = $( '#excerpt' );

			if( excerpt_box.length < 1 ) return;

			chars_limit = limit;

			$( '#postexcerpt > .hndle' ).append( '<div class="sm_excerpt_chars_counter">Characters remaining: <span></span></div>' );
			$( '#postexcerpt > .hndle' ).append( '<span class="sm_excerpt_error_content"></span>' );
			$( '#tagsdiv-post_tag > .hndle' ).append( '<span class="tags-validate-error"></span>' );

			excerpt_box.limit_chars({
				limit   : chars_limit,
				counter : '.sm_excerpt_chars_counter'
			});

			excerpt_box.keyup(function()
			{
				validate_field();
			});

			error_content = $( '.sm_excerpt_error_content' );
			error_content_tags = $( '.tags-validate-error' );
			error_content_tags.hide();
			error_content.hide();

			var post_status_box = $( '#post_status' );
			post_status_box.change(function()
			{
				post_status = $( this ).val();
			});

			post_status = post_status_box.val();

			$( '#post' ).submit(function()
			{
				if( false == validate_field() )
				{
					$( '#ajax-loading' ).css( {visibility : 'hidden'} );
					$( '#publish' ).removeClass( 'button-primary-disabled' );

					show_error();

					return false;
				}
			});

			$( '#publish' ).click(function()
			{
				if( $( '#original_publish' ).val().toLowerCase() == 'publish' ) post_status = 'publish';
			});

			$( '#save-post' ).click(function()
			{
				post_status = post_status_box.val();
			});
		}
	}
}();

})(jQuery);