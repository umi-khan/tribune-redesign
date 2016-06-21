(function($)
{

$( document ).ready(function()
{	
	var options = {
		items                  : SM_author_info.all_authors,
		selected_items         : SM_author_info.current_authors,
		container              : '#sm_authors',
		item_content_id_prefix : 'sm_author-',
		remote_url             : ajaxurl,
		remote_add_action      : 'sm_add_author',
		remote_remove_action   : 'sm_remove_author',
		post_id                : $( '#sm_author_post_id' ).val()
	};
	var sm_authors_autocomplete = new SM_autocomplete();
	sm_authors_autocomplete.init( options );
});

})(jQuery);