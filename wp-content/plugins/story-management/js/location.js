(function($)
{

$( document ).ready(function()
{
	// sort the location
	SM_location_info.all_locations.sort(function(a, b)
	{
		var a_name = a.name.toLowerCase();
		var b_name = b.name.toLowerCase();
		
		if( a_name < b_name ) return -1;

		if( a_name > b_name ) return 1;

		return 0;
	});

	// initialize the autocomplete
	var options = {
		items                  : SM_location_info.all_locations,
		selected_items         : SM_location_info.current_locations,
		container              : '#sm_locations',
		item_content_id_prefix : 'sm_location-',
		remote_url             : ajaxurl,
		remote_add_action      : 'sm_add_location',
		remote_remove_action   : 'sm_remove_location',
		post_id                : $( '#sm_location_post_id' ).val()
	};

	var sm_locations_autocomplete = new SM_autocomplete();
	sm_locations_autocomplete.init( options );
});

})(jQuery);