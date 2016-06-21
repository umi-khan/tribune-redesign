/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var VM = {
	main    : {},
	popup   : {},
	editor  : {},
	gallery : {}
};

(function($)
{

$( window ).load(function()
{
	// initialize the main script
	VM.main.init();
});

VM.main = function()
{
	return {
		init : function()
		{
			var video_edit_img = $( '.video_edit_img' );
			var can_edit_video = ( video_edit_img.length > 0 );

			if( false == can_edit_video ) return;
			
			video_edit_img.hide();
			
			video_edit_img.parents( '.video' ).hover(
				function() {$( this ).find( '.video_edit_img' ).show();},
				function() {$( this ).find( '.video_edit_img' ).hide();}
			);
		}
	}
}();

})(jQuery);