/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($)
{

IM_popup = function()
{
	var popup;
	var content_area;
	var loading_img;

	return {
		init : function()
		{
			// setup the area where editor is loaded
			content_area = $( '#im_popup_content' );

			// setup the popup
			popup = $( '#im_popup' );

			popup.dialog({
				autoOpen   : false,
				width      : parseInt( popup.css( 'width' ) ),
				height     : parseInt( popup.css( 'height' ) ),
				modal      : true,
				draggable  : false,
				resizable  : false
			});

			// setup the loading image
			loading_img = $( '#im_popup_loading_img' );
		},
		
		open : function(title)
		{
			popup.dialog( 'option', 'title', title );
			popup.dialog( 'open' );

			IM_popup.set_content('');
		},

		close : function()
		{
			IM_popup.set_content('');
			popup.dialog( 'close' );
		},

		set_content : function(content)
		{
			content_area.show().html( content );
		},

		show_loading : function()
		{
			$( '#im_popup_wrap' ).fadeTo( 'fast', 0.2, function()
			{
				loading_img.show();
				loading_img.offset({
					top  : popup.offset().top + popup.height() / 2 - loading_img.height() / 2,
					left : popup.offset().left + popup.width() / 2 - loading_img.width() / 2
				});
			});
		},

		hide_loading : function()
		{
			$( '#im_popup_wrap' ).fadeTo( 'medium', 1 );

			loading_img.hide();
		}
	}
}();

})(jQuery);