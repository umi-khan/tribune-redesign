LM_rpc = function()
{
	/*
    * XML RPC settings below
    */
   
   var xmlrpc_url = LM_config.base_url + "/xmlrpc.php";
   
	var update_story = function(story_obj,lm_before_ajax,lm_after_ajax)
	{
		var remote_method_name = 'lm_update_story';
		var params = [story_obj];
		
		var data = xml_json_rpc_helper.xmlize_request(remote_method_name, params);
		
		jQuery.ajax(
		    {
				url	    : xmlrpc_url,
				type	    : "POST",
				contentType : "text/xml",
				processData : false,
				data	    : data,
				dataType    : "xml",
				beforeSend  : lm_before_ajax,
				complete    : lm_after_ajax
		    }
		);
	}

	var update_layout = function(page, category_id, group_id, old_story, new_story, do_cycle, lm_before_ajax, lm_after_ajax)
	{
		var remote_method_name = 'lm_update_layout';

		var params =
		{
			page			: page,
			category_id	: category_id,
			group_id		: group_id,
			old_story	: old_story,
			new_story	: new_story,
			do_cycle		: do_cycle
		};
		
		var data = xml_json_rpc_helper.xmlize_request(remote_method_name, params);
		
		jQuery.ajax(
		    {
				url	    : xmlrpc_url,
				type	    : "POST",
				contentType : "text/xml",
				processData : false,
				data	    : data,
				dataType    : "xml",
				beforeSend  : lm_before_ajax,
				complete    : lm_after_ajax
		    }
		);
	}
	
   return {
	      update_story	: update_story,
			update_layout	: update_layout
	   };

}();