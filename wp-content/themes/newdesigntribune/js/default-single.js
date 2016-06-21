/**
 *
 *	Express Single Story Javascript functionality.
 *	Imported only on Single Story page
 *
 *
*/

(function($)
{
	$( document ).ready(function() { 
		
		var target_image   = $( '.story-image img' );
		var target_caption = $( '.story-image .caption' );

		image_click = function($selected_image)
		{
			var source       = $( $selected_image );
			var large_src    = source.attr( 'longdesc' );
			
			target_image.fadeTo( 'medium', 0.5 );

			var img = new Image();
			img.onload = function()
			{
				target_image.fadeTo( 'fast', 1, function()
				{
					target_image.attr({
						src    : large_src,
						width  : source.attr( 'largewidth' ),
						height : source.attr( 'largeheight' )
					});
				});
			};
			img.src = large_src;
			
			target_caption.text( source.attr( 'alt' ) );
			return false;
		};
		
		$('.story-carousel').carousel({callback:image_click});
		
		CommentOperations.init();
		
	});
	
	var CommentOperations = function()
	{
		var comments_shown = 50;
		var comments_selector = '#comments ul.commentlist li';
		var more_comments_selector = '#comments .more-comments';
		
		var init = function()
		{
			$(comments_selector+':gt('+(comments_shown-1)+')').addClass('hide-comment');
			var $comments = $(comments_selector);
			if($comments.length > comments_shown) $(more_comments_selector).show();
			$('#comments .more-comments').click( more_comments );
			$('#comments .ul-tabs li a').click( most_recommended_comments );
		};
		
		var more_comments = function(e)
		{
			e.preventDefault();
			var $more_link = $(this);
			var $pagination_div = $('#comments .comment-pagination');

			var $hidden_comments = $(comments_selector+'.hide-comment:lt('+comments_shown+')');

			$hidden_comments.removeClass('hide-comment');
			
			if($(comments_selector+'.hide-comment').length == 0)
			{
				$more_link.hide();
				$pagination_div.show();
			}
		};
		
		var most_recommended_comments = function(e)
		{
			e.preventDefault();
			var $tab = $(e.target);
			if( !$tab.hasClass('current') )
			{
				var $comments_li = $(comments_selector);
				var $li_parent = $comments_li.parent();
				$li_parent.fadeOut(250,function(){
					if($comments_li.length > 1)
					{
						var sort_method;
						
						if( $tab.hasClass('recommended-comments') )
						{
							sort_method = sort_comments_likes;
						}
						else if( $tab.hasClass('all-comments') )
						{
							sort_method = sort_comments_ids;
						}
						
						$comments_li.sort( sort_method );
						$comments_li.appendTo( $li_parent );
						$comments_li.filter(':lt('+comments_shown+')').removeClass('hide-comment');
						$comments_li.filter(':gt('+comments_shown+')').addClass('hide-comment');
						if( $comments_li.length > comments_shown )$(more_comments_selector).show();
					}
				
					$tab.parent().siblings('li').children('a').removeClass('current');
					$tab.addClass('current');
					$li_parent.fadeIn(250);
				});
			}
		};
		
		var sort_comments_likes = function(a, b)
		{
			// a and b are the two elements passed to be sorted out of the array
			var likes_selector = '.comments-like span';
			var $a = $(a);
			var $b = $(b);
			var a_likes = ( $a.find(likes_selector).length != 0 ) ? parseInt( $a.find(likes_selector).text() ) : 0;
			var b_likes = ( $b.find(likes_selector).length != 0 ) ? parseInt( $b.find(likes_selector).text() ) : 0;
			
			return ( a_likes < b_likes ) ? 1 : ( a_likes > b_likes ) ? -1 : 0 ; // returning opposite values to sort desc
		};
		
		var sort_comments_ids = function(a, b)
		{
			// a and b are the two elements passed to be sorted out of the array
			var a_id = $(a).attr('id').match(/.*li-comment-([0-9]+).*/)[1];
			var b_id = $(b).attr('id').match(/.*li-comment-([0-9]+).*/)[1];
			return ( a_id < b_id ) ? -1 : ( a_id > b_id ) ? 1 : 0;
		};
		
		return {
			init : init
		};
	}();
	
   /************ Slide JS ***********/      
   var $adjacent = document.getElementById('adjacent');
   if( $adjacent != null )
   {
      var slideRightStyle = $adjacent.style.right;
       $(window).scroll(function() 
      {
         var canvasHeight      = document.documentElement.clientHeight;
         var currWinScrollTop  = $(window).scrollTop();      
         var storyHeight       = $('.story-content').height() + $('.story-content').offset().top;		
         $adjacent.style.right = ( currWinScrollTop >= storyHeight - canvasHeight ) ? 0 : slideRightStyle;
      });
   }  
   
})(jQuery);