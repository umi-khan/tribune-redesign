/* global screenReaderText */
/**
 * Theme functions file.
 *
 * Contains handlers for navigation and widget area.
 */

$(window).load(function() {
    	var topValw = $("#ad-leaderboard-top iframe").css("width");
		var topValh = $("#ad-leaderboard-top iframe").css("height");
		$('.ad-leaderboard-container').css("width", topValw);
 		$('.ad-leaderboard-container').css("height", topValh);
});

(function($)
{
  
		$( document ).ready( function() {

		$("#main-menu .search-menu a").click(function(){
    		$("#search").slideToggle(1000);
		});

	    		// Carousels
		$( '.opinion .carousel' ).carousel( { paginationContainerSelector : '.opinion .carousel-pagination' } );
		$( '.videos .carousel' ).carousel( { paginationContainerSelector : '.videos .carousel-pagination' } );
		$( '.letters-widget .carousel' ).carousel( { paginationContainerSelector : '.letters-widget .carousel-pagination' } );
		$( '.slideshows .carousel' ).carousel( { paginationContainerSelector : '.slideshows .carousel-pagination' } );



		loadCarouselWidget('videos-widget');
		loadCarouselWidget('slideshows-widget');

		$('.read-full-story-button').click(function() {
    	$( 'div' ).removeClass( 'read-full' );
  		});


		if ($(window).width() <= 1000) {

		//$('.main-navigation #main-menu').prepend('<li class="menu-heading"><div class="nav-icon"><span class="small-nav"><i class="fa fa-navicon"></i></span></div></li>');
		$('.nav-icon').click(function(e){
				$('.main-navigation #main-menu').slideToggle();
				if($('.nav-icon').find('i').hasClass('fa-navicon')){
					$('.nav-icon').find('i').removeClass('fa-navicon');
					$('.nav-icon').find('i').addClass('fa-remove');
					$('.nav-icon').find('i').css('color','red');
					$('.search-icon').css('display', 'none');
					$('body').css('overflow-y', 'hidden');
				}else{
					$('.nav-icon').find('i').addClass('fa-navicon');
					$('.nav-icon').find('i').removeClass('fa-remove');
					$('.nav-icon').find('i').css('color','#848484');
					$('.search-icon').css('display', 'block');
					$('body').css('overflow-y', 'auto');
				}
				
			});

			$('.search-icon').click(function(e){
				$("#search").slideToggle('fast');
				if($('.search-icon').find('i').hasClass('fa-search')){
					$(this).find('i').removeClass('fa-search');
					$(this).find('i').addClass('fa-remove');
					$(this).find('i').css('color','red');
				}else{
					$(this).find('i').addClass('fa-search');
					$(this).find('i').removeClass('fa-remove');
					$(this).find('i').css('color','#848484');
				}
				
			});

		$('ad-leaderboard-container .textwidget').replaceWith($('ad-leaderboard-container .textwidget').contents());
		$('#ad-leaderboard-top').rad({maxWidth:"970"});
    	$('#home-small-lb').rad({maxWidth:"468"});
    	$('#ad-leaderboard-bottom').rad({maxWidth:"970"});

		}

		function loadCarouselWidget( elementId )
			{
				jQuery('#' + elementId + ' .carousel').carousel({
				prevButtonSelector : '#' + elementId + ' .prev',
				nextButtonSelector : '#' + elementId + ' .next',
				pagination : false
			});
		jQuery('#' + elementId + ' .controls').show();
			}
		});

		if( $('#cfct-search').length > 0 )
		{
			var $searchBox  = $('#cse-search-box').find('.text');
			var searchBoxBg = $searchBox.css('background-image');
			
			$('#cfct-search').find('.text')
				.css(
				{
					'background-image'    : searchBoxBg,
					'background-repeat'   : 'no-repeat',
					'background-position' : $searchBox.css('background-position')
				})
				.focus(function()
				{
					$(this).css('background-image' , 'none');
				})
				.blur(function()
				{
					if( $(this).val() == '' )
						$(this).css('background-image' , searchBoxBg );
				}
			);
		}

	

} )( jQuery );
