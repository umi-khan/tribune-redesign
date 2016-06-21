(function($)
{
	$( window ).load(function()
	{
		var post_id = ( $( '#im_post_id' ).length > 0 ) ? $( '#im_post_id' ).val() : 0;

		IM_popup.init( post_id );
		IM_editor.init( post_id );
		IM_gallery.init( post_id );
		IM_library.init( post_id );

		// setup the observers
		IM_gallery.add_observer( IM_editor );

		IM_editor.add_observer( IM_gallery );
		IM_editor.add_observer( IM_library );
		
		IM_library.add_observer( IM_editor );
		IM_library.add_observer( IM_gallery );

		// setup the library open handler
		$( '#im_upload' ).click(function()
		{
			IM_library.open();
		});
	});
})(jQuery);