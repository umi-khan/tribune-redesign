(function($){
	function Carousel(obj,conf)
	{
		var self = this, _isScrolling = false, 
         $current, $root, $itemsContainer, $items, $prevButton, $nextButton,
         _rootCenterOffsetLeft, _rootOffsetLeft, _rootOffsetRight, _rootWidth,
         _rootCenterOffsetTop, _rootOffsetTop, _rootOffsetBottom, _rootHeight,
         pageCount, currentPage, $_lastItem, $_firstItem,
         _defaultPaginationContainerClass = 'carousel-pagination', 
         _itemsContainerWidth, _itemsContainerHeight,
         _activeCarouselClass = 'active-carousel';
		
		var settings = {
			itemsContainerSelector : '.items',
			itemSelector : '.item',
			nextButtonSelector : null,
			prevButtonSelector : null,
			callback : '', 
			pagination : true, 
			paginationContainerSelector : null,
         vertical : false
		};
		
		function init(root,userSettings)
		{
			$root = $(root);
			if ( $root.length == 0 || $root.hasClass(_activeCarouselClass) ) return;
			
			$itemsContainer = $(settings.itemsContainerSelector,$root);
			if ($itemsContainer.length == 0) return;
			
			$items = $itemsContainer.children(settings.itemSelector);
			if ($items.length == 0) return;
			
			$_lastItem = $items.last();
			$_firstItem = $items.first();

			var lastItemMarginRight = $_lastItem.css('marginRight');
			if( lastItemMarginRight == 'auto' ) lastItemMarginRight = 0; //Prevent NaN Issue

			var firstItemMarginLeft = $_firstItem.css('marginLeft');
			if( firstItemMarginLeft == 'auto' ) firstItemMarginLeft = 0; //Prevent NaN Issue

         var lastItemMarginBottom = $_lastItem.css('marginBottom');
			if( lastItemMarginBottom == 'auto' ) lastItemMarginBottom = 0; //Prevent NaN Issue

			var firstItemMarginTop = $_firstItem.css('marginTop');
			if( firstItemMarginTop == 'auto' ) firstItemMarginTop = 0; //Prevent NaN Issue
         
         if(typeof userSettings == 'object') $.extend(settings,userSettings);
         
         if( false !== settings.vertical )	
         {
            _itemsContainerHeight = Math.round( ( $_lastItem.offset().top + $_lastItem.outerHeight() + parseInt(lastItemMarginBottom) )
									- ( $_firstItem.offset().top - parseInt(firstItemMarginTop) ) );			
            $itemsContainer.height(_itemsContainerHeight);
         
         }
         else
         {
            _itemsContainerWidth = Math.round( ( $_lastItem.offset().left + $_lastItem.outerWidth() + parseInt(lastItemMarginRight) )
									- ( $_firstItem.offset().left - parseInt(firstItemMarginLeft) ) );			
            $itemsContainer.width(_itemsContainerWidth);
         }   			
			         
			// modifying styles
			$root.css('overflow','hidden');
			$itemsContainer.css('position','relative');
						
         _rootOffsetLeft = $root.offset().left;
         _rootCenterOffsetLeft = _rootOffsetLeft + ($root.outerWidth() / 2);
         _rootWidth = $root.width();
         _rootOffsetRight = _rootOffsetLeft + _rootWidth;         			
       
			_rootOffsetTop = $root.offset().top;
         _rootCenterOffsetTop = _rootOffsetTop + ($root.outerHeight() / 2);
         _rootHeight = $root.height();
         _rootOffsetBottom = _rootOffsetTop + _rootHeight;         

         // pagination
			pageCount = Math.ceil( ( false !== settings.vertical ? _itemsContainerHeight / $root.height() : _itemsContainerWidth / $root.width() ) );         
			
         if(pageCount == 1) settings.pagination = false;
         pageCount = parseInt(pageCount) || 0;
			if(settings.pagination)
			{
				var paginationContainer;
				if ( settings.paginationContainerSelector != null ) paginationContainer = $(settings.paginationContainerSelector);
				else
				{
					paginationContainer = $root.find('.'+_defaultPaginationContainerClass);
					if(paginationContainer.length == 0)
					{
						paginationContainer = $('<div class="'+_defaultPaginationContainerClass+'"></div>');
						$root.append(paginationContainer);
					}
				}

				var pageClick = function(event)
				{
					event.preventDefault();
					_gotoPage(event.data.page);
				};
				for(var i = 1; i <= pageCount; i++)
				{
					var pageLinkClasses = 'carousel-page-'+i;
					var pageLink = $('<a href="#" class="'+pageLinkClasses+'"></a>');
					
					pageLink.bind('click',{page:i},pageClick);
					paginationContainer.append(pageLink);
				}
			}
			
			//setting current Item and current Page
			var _firstCurrent = $items.filter('.current');
			_firstCurrent = ( _firstCurrent.length > 0 ) ? _firstCurrent.eq(0) : $_firstItem;  
			select(_firstCurrent);
			_updatePagination();
			
			$nextButton = (settings.nextButtonSelector == null) ? $('.next',$root) : $(settings.nextButtonSelector);
			$prevButton = (settings.prevButtonSelector == null) ? $('.prev',$root) : $(settings.prevButtonSelector);
			_updateNextPreviousButtonState();
			
			if($nextButton.length != 0) $nextButton.click(self.nextPage);
			if($prevButton.length != 0) $prevButton.click(self.prevPage);
			
			
			$items.click (function(e)
			{
				var href = ($(e.target).attr('href') != undefined) ? $(e.target).attr('href') : $(e.target).parent().attr('href');
				if( href == undefined || href.indexOf('http') == -1 )
				{
					e.preventDefault();
					select(e.currentTarget);
				}
			});
			
			$root.addClass(_activeCarouselClass);
		}

		self.prevPage = function()
		{
			if(!$prevButton.hasClass('disabled'))
			{
				_gotoPage(currentPage-1);
			}
			return false;	//preventing default behaviour
		};
		
		self.nextPage = function()
		{
			
			if(!$nextButton.hasClass('disabled'))
			{
				_gotoPage(currentPage+1);
			}
			return false;	//preventing default behaviour
		};
		
		function _gotoPage(page)
		{
			if(page > pageCount) page = pageCount;
			else if (page < 1) page = 1;			
			var 
         desiredOffset = ( (page-1) * ( settings.vertical ? _rootHeight : _rootWidth ) ),
			scrollOffset = ( settings.vertical ? _rootOffsetTop - $itemsContainer.offset().top : _rootOffsetLeft - $itemsContainer.offset().left ) - desiredOffset;
			_scroll(scrollOffset, _setFirstVisibleItemToCurrent);
		};

		function select(index_or_element)
		{
			var $selectedItem = (typeof index_or_element == 'number') ? $items.eq(index_or_element) : $(index_or_element);
			if ($selectedItem.length == 0 || $items.index($selectedItem) < 0) return; // whether selected item exists
			
			// update current
			_updateCurrent($selectedItem);
			
			// scroll
			var 
         selectedItemWidth  = $selectedItem.outerWidth(), selectedItemHeight = $selectedItem.outerHeight(),
			elCenterOffsetLeft = $selectedItem.offset().left + (selectedItemWidth / 2),
         elCenterOffsetTop  = $selectedItem.offset().top + (selectedItemHeight / 2),
         scrollOffset;
         
         if( settings.vertical )			            
            scrollOffset = parseInt( ( _rootCenterOffsetTop - elCenterOffsetTop ) / selectedItemHeight) * $selectedItem.outerHeight(true); // for even number of visible items, also taking in account margins         
         else                     
            scrollOffset = parseInt( ( _rootCenterOffsetLeft - elCenterOffsetLeft ) / selectedItemWidth) * $selectedItem.outerWidth(true); // for even number of visible items, also taking in account margins
         
			if( scrollOffset !== 0 ) _scroll(scrollOffset);
		}


		var _scroll = function(offset,onComplete)
		{
			if(_isScrolling === true || offset === 0) return;
			
			if(offset > 0)
			{
				var firstItemMarginLeft = $_firstItem.css('marginLeft');
				if( firstItemMarginLeft == 'auto' ) firstItemMarginLeft = 0; //Prevent NaN Issue

            var firstItemMarginTop = $_firstItem.css('marginTop');
				if( firstItemMarginTop == 'auto' ) firstItemMarginTop = 0; //Prevent NaN Issue
            
            if( settings.vertical )					               
               scrollOffset = Math.min((_rootOffsetTop - ( $_firstItem.offset().top - parseInt( firstItemMarginTop ) ) ), offset);            
            else            
               scrollOffset = Math.min((_rootOffsetLeft - ( $_firstItem.offset().left - parseInt( firstItemMarginLeft ) ) ), offset);				
			}
			else // for negative offsets
			{
				var $lastItem = $_lastItem;

				var lastItemMarginRight = $_lastItem.css('marginRight');
				if( lastItemMarginRight == 'auto' ) lastItemMarginRight = 0; //Prevent NaN Issue

            var lastItemMarginBottom = $_lastItem.css('marginBottom');
				if( lastItemMarginBottom == 'auto' ) lastItemMarginBottom = 0; //Prevent NaN Issue
            
				if( settings.vertical )	
            {
               var lastItemBottomEdge = $lastItem.offset().top + $lastItem.outerHeight() + parseInt( lastItemMarginBottom ); // also taking in acount margins
               if ( lastItemBottomEdge < _rootOffsetBottom ) return;
               scrollOffset = Math.max((_rootOffsetBottom - lastItemBottomEdge), offset);
            }
            else
            {
               var lastItemRightEdge = $lastItem.offset().left + $lastItem.outerWidth() + parseInt( lastItemMarginRight ); // also taking in acount margins
               if ( lastItemRightEdge < _rootOffsetRight ) return;
               scrollOffset = Math.max((_rootOffsetRight - lastItemRightEdge), offset);
            }
				
			}
			
			if(scrollOffset != 0)
			{
            var animate = settings.vertical ? { "top": '+='+scrollOffset+'px' } : { "left": '+='+scrollOffset+'px' };
				_isScrolling = true;            
				$itemsContainer.animate(animate, 400, function(){
					if(typeof onComplete == 'function') onComplete();
					_updatePagination();
					_updateNextPreviousButtonState(); // disable next/previous buttons
					_isScrolling = false;
				});
			}
		};

		var _setFirstVisibleItemToCurrent = function() 
		{
			var $first;
			var itemsLength = $items.length;
			
			for( var i = 0; i < itemsLength; i++)
			{
            if( settings.vertical && $items.eq(i).offset().top >= _rootOffsetTop )
            {
               $first = $items.eq(i);
               break;
            }
            else if( false === settings.vertical && $items.eq(i).offset().left >= _rootOffsetLeft )
            {
               $first = $items.eq(i);
               break;
            }            				
			}
			_updateCurrent($first);
		};
		
		var _updatePagination = function()
		{
			//updating page number
			var newCurrent, currentPageClass = 'carousel-current-page';                  
         
         if( settings.vertical )
			{
            var lastItemBottomEdge = $_lastItem.offset().top + $_lastItem.outerHeight();
            if(lastItemBottomEdge <= _rootOffsetBottom) newCurrent = pageCount;
            else newCurrent = Math.ceil(( ( Math.abs($itemsContainer.offset().top - _rootOffsetTop) ) + $current.height()) / _rootHeight );			
         }
         else
         {
            var lastItemRightEdge = $_lastItem.offset().left + $_lastItem.outerWidth();
            if(lastItemRightEdge <= _rootOffsetRight) newCurrent = pageCount;
            else newCurrent = Math.ceil(( ( Math.abs($itemsContainer.offset().left - _rootOffsetLeft) ) + $current.width()) / _rootWidth );			
         }
			
			if( currentPage !== undefined && newCurrent === currentPage ) return;
			
			currentPage = newCurrent;
			// applying styles
			if(settings.pagination) 
			{
				var paginationContainer;
				if(settings.paginationContainerSelector != null ) paginationContainer = $(settings.paginationContainerSelector);
				else paginationContainer =  $root.find('div.carousel-pagination');
				paginationContainer.find('a').removeClass(currentPageClass);
				paginationContainer.find('a.carousel-page-'+currentPage).addClass(currentPageClass);
			}
		};
		
		var _updateNextPreviousButtonState = function()
		{
			if(currentPage === 1) $prevButton.addClass('disabled');
			else $prevButton.removeClass('disabled');
			
			if(currentPage == pageCount) $nextButton.addClass('disabled');
			else $nextButton.removeClass('disabled');
		};
		
		var _updateCurrent = function(newCurrent)
		{
			if(undefined !== $current) $current.removeClass('current');
			$current = newCurrent;
			$current.addClass('current');

			if(typeof settings.callback == 'function') settings.callback($current);
		};
		
		// Move to Next Item
		self.next = function()
		{
			select($current.next());
			return $current;
		};
		// Move to Previous Item		
		self.prev = function()
		{
			select($current.prev());
			return $current;
		};
		
		// initializing
		init(obj,conf);
		
	}

	$.fn.carousel = function(conf)
	{
		var carousels = new Array();
		this.each(function(i){
			carousels[i] = new Carousel(this,conf);
		});
		if(carousels.length == 1)
			return carousels[0];
		else return carousels;
	};

})(jQuery);