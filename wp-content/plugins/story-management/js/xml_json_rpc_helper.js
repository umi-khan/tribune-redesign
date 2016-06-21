xml_json_rpc_helper = function()
{
	// handle html response
	var html_response_handler = function(response_obj)
	{
		var response;

		if( response_obj.responseText.search(/<title>WordPress &rsaquo; Error<\/title>/) != -1 )
		{
			response = response_obj.responseText.match(/<p>(.*)<\/p>/);
			response = response[1];

			return error_handler( 110, response );
		}
		else if( response_obj.status != 200 )
			return error_handler( 120, "An error occurred, please try again!" );

		return { html: response_obj.responseText }
	}

	// handle json response
	var json_response_handler = function(response_obj)
	{
		var data;

		// try to parse the json response if not a valid json return an error object
		try
		{
			data = jQuery.parseJSON( response_obj.responseText );
		}
		catch( error )
		{
			return error_handler( 0, "Invalid json" );
		}

		// if the json response object consists of an error return an error object
		if( data.error != null )
			return error_handler( 10, data.error );

		// if the json response is a valid result and not an error return the result
		return { json: data.result }
	}

	// handle error
	var error_handler = function(error_code, error_msg)
	{
		return { 
			error : { code: error_code, message: error_msg }
		}
	}

	return {
		// convert the request in xml-rpc request format
		xmlize_request : function(remote_method, param)
		{
			var msg = new XMLRPCMessage( remote_method );

			if( param != null )
			{
				if( param.length )
				{
					for( var i = 0; i < param.length; i++ )
					{
						if( param[i] != null ) msg.addParameter( param[i] );
					}
				}
				else msg.addParameter( param );
			}

			return msg.xml();
		},
		
		// parse the response and return either html or json or error object
		parse_response : function(response_obj)
		{
			if( response_obj.getResponseHeader( "Content-Type" ).search(/text\/html/) != -1 )
				return html_response_handler( response_obj );
			else if( response_obj.getResponseHeader( "Content-Type" ).search(/application\/json/) != -1 )
				return json_response_handler( response_obj );
			else
				return error_handler( "500", "Invalid response" );
		}
	}
}();