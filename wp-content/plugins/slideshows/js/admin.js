/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


(function($)
{

	$( document ).ready( function()
	{		
		Slideshows_cat_admin.init();
	});

	Slideshows_cat_admin = function()
	{
		var $categoryChecklist = $( '#categorychecklist , .category-checklist' ).find('input');
		//var $categoryChecklistInline = $( '.category-checklist' ).find('input');
		var errorContent;
		var postStatus;
		var SlideshowsCategories = new Array();		

		var validate_field = function()
		{
			if( postStatus != 'publish' ) return true;

			var count_selected_cat = 0
			$categoryChecklist.each(function()
			{				
				if( this.checked )
					count_selected_cat++;
			})
			if( count_selected_cat == 0 ) return false;
			
			errorContent.removeClass( 'error' );
			errorContent.hide();
			$categoryChecklist.closest('div').css({'border-color': ''});
			return true;
		}

		var show_error = function()
		{
			errorContent.addClass( 'error' );

			var error_txt;
			
			error_txt = 'Atleast one category should be selected for this slideshow!';
			$categoryChecklist.closest('div').css({'border-color': '#cc0000'});
			$categoryChecklist.focus();
			errorContent.find('p').text( error_txt );
			errorContent.show();
		}

		return {
			init : function()
			{
				if( $categoryChecklist.length < 1 ) return;
				
				$categoryChecklist.each(function()
				{
					for( var cat in SlideshowsCats )
					{						
						if( SlideshowsCats[cat] == $(this).val() ) return;
					}
					
					$(this).attr( 'disabled', 'disabled' );
				});				
				

				if( $('#slideshow-message').length == 0 )		//if message container not found, create it
					 jQuery('<div></div>').attr({'id': 'slideshow-message'}).insertBefore($('#post'));
				
				errorContent = $( '#slideshow-message' ).append($('<p></p>'));
				errorContent.hide();
							
				
				var post_status_box = $( '#post_status' );
				post_status_box.change(function()
				{
					postStatus = $( this ).val();
				});

				postStatus = post_status_box.val();
				
				$( '#post' ).submit(function()
				{
					if( false == validate_field() )
					{
						$( '#ajax-loading' ).css( {visibility : 'hidden'} );
						$( '#publish' ).removeClass( 'button-primary-disabled' );
						$( '#save-post').removeClass( 'button-disabled' );
						
						show_error();

						return false;
					}
				});

				$( '#publish' ).click(function()
				{
					if( $( '#original_publish' ).val().toLowerCase() == 'publish' ) postStatus = 'publish';
				});

				$( '#save-post' ).click(function()
				{
					postStatus = post_status_box.val();
				});

				
			}
		}

		init();
	}();
})(jQuery);