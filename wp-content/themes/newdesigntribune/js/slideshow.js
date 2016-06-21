/**
 *
 *	Express Slideshow Javascript functionality.
 *	Imported only on Slideshow page
 *
 *
*/



(function($)
{
	$(document).ready(function()
	{
		Gallery.init();		
	});

	var Gallery =
	{
		settings : {
			$galleryContainer    : $("#picture-gallery"),
			prevSelector         : '.prev-item',
			nextSelector         : '.next-item',
			carouselSelector     : '.carousel',
         pagination           : false,
         vertical             : true         
		},

		carouselContainer : null,
		carousel : {},
      carouselPrevSelector : null,
      carouselNextSelector : null,
      
		allImgs : new Array(),//new array for all the image urls
		currentIndex : 0,

		init : function( params )
		{
			$.extend( this.settings, params );
			this.registerBehaviours();
			this.load();
		},

		registerBehaviours : function()
		{
			$(window).load(function()
			{
				Gallery.settings.$galleryContainer.find(Gallery.settings.prevSelector).click(Gallery.prev);
				Gallery.settings.$galleryContainer.find(Gallery.settings.nextSelector).click(Gallery.next);
			});
		},


		load : function()
		{
			this.carouselContainer =  this.settings.$galleryContainer.find(this.settings.carouselSelector);         
         
			this.allImgs = this.preloadImages();
         this.settings.callback = this.carouselCallback;
         
         var galleryContainerHeight = parseInt(this.settings.$galleryContainer.height()) || 0;
                  
         galleryContainerHeight -= parseInt( this.carouselContainer.css('marginTop') ) || 0; //Prevent NaN Issue
         galleryContainerHeight -= parseInt( this.carouselContainer.css('marginBottom') ) || 0; //Prevent NaN Issue
         galleryContainerHeight -= parseInt( this.carouselContainer.css('paddingTop') ) || 0; //Prevent NaN Issue
         galleryContainerHeight -= parseInt( this.carouselContainer.css('paddingBottom') ) || 0; //Prevent NaN Issue
         
         
         galleryContainerHeight -= parseInt( this.carouselContainer.parent().css('marginTop') ) || 0; //Prevent NaN Issue
         galleryContainerHeight -= parseInt( this.carouselContainer.parent().css('marginBottom') ) || 0; //Prevent NaN Issue
         galleryContainerHeight -= parseInt( this.carouselContainer.parent().css('paddingTop') ) || 0; //Prevent NaN Issue
         galleryContainerHeight -= parseInt( this.carouselContainer.parent().css('paddingBottom') ) || 0; //Prevent NaN Issue
                           
         if( this.carouselContainer.siblings('a.prev').length == 1 )          
         {
            this.settings.prevButtonSelector = this.carouselPrevSelector = this.carouselContainer.siblings('a.prev');
            if( this.carouselContainer.siblings('a.prev').css('position') != 'absolute' )
               galleryContainerHeight -= parseInt( this.carouselPrevSelector.height() ) || 0;
         }   
         if( this.carouselContainer.siblings('a.next').length == 1 ) 
         {
            this.settings.nextButtonSelector = this.carouselNextSelector = this.carouselContainer.siblings('a.next');
            if( this.carouselContainer.siblings('a.next').css('position') != 'absolute' )
               galleryContainerHeight -= parseInt( this.carouselNextSelector.height() ) || 0;
         }
                              
			this.carousel = this.carouselContainer.height( galleryContainerHeight ).css("max-height", galleryContainerHeight + "px" ).carousel( this.settings );

			if ( this.allImgs.length > 1 && $(this.settings.nextSelector).hasClass( 'disabled' ) )
				$(Gallery.settings.nextSelector).removeClass( 'disabled' );

			this.carouselContainer.find('a.prev, a.next, '+this.settings.prevSelector+', '+this.settings.nextSelector).attr('style', 'display:block;');
         if( this.carouselPrevSelector != null ) this.carouselPrevSelector.attr('style', 'display:block;');
         if( this.carouselNextSelector != null ) this.carouselNextSelector.attr('style', 'display:block;');
		},

		preloadImages : function ()
		{
			 var items = Gallery.carouselContainer.find( '.items .item' );

			 var imgs = new Array();

			 if( items != null && items.length > 0 && items != '' )
			 {
					var k = 0;
					$(items).each(
						function()
						{
							imgs[k] = new Image(); //new img obj
							imgs[k].src = $(this).attr('longdesc');
							imgs[k].alt = $(this).attr('alt');
							k++;
						}
					);
			 }
			 return imgs;
		},

		next : function()
		{			
			Gallery.carousel.next();
		},

		prev : function()
		{
			Gallery.carousel.prev();
		},

		enableDisableNav: function ()
		{
			if ( Gallery.itemsCount <= 1 )
				return;

			if ( Gallery.currentIndex <= 0 )
			{
				$(Gallery.settings.prevSelector).addClass('disabled');
				if( $(Gallery.settings.nextSelector).hasClass('disabled') )
					$(Gallery.settings.nextSelector).removeClass('disabled');
			}
			else
			{
				if ( Gallery.currentIndex == (parseInt( Gallery.allImgs.length ) - 1 ) )
					$(Gallery.settings.nextSelector).addClass('disabled');
				else if( $(Gallery.settings.nextSelector).hasClass('disabled') )
					$(Gallery.settings.nextSelector).removeClass('disabled');

				if($(Gallery.settings.prevSelector).hasClass('disabled'))
					$(Gallery.settings.prevSelector).removeClass('disabled');
			}
		},
		carouselCallback : function( currentItem )
		{
			var index = Gallery.carouselContainer.find('.items .item').index(currentItem);

			if ( index == Gallery.currentIndex )
				return false;

			Gallery.currentIndex = index;
			Gallery.enableDisableNav();

			var item = Gallery.allImgs[Gallery.currentIndex];

			var imageContainer   = Gallery.settings.$galleryContainer.find('.slideshow-container');
			var captionContainer = imageContainer.find('p.caption');
			var img = imageContainer.find('img');

			if( img.length > 0 )
			{

				item.width = $(img).attr('width');
				item.height = $(img).attr('height');
				if ( item != img )
					img.replaceWith(item);

				captionContainer.fadeOut( 'slow' , function(){
					$(this).html(item.alt).fadeIn( 'slow' );
				});

			}
		}
	}
	
})(jQuery);