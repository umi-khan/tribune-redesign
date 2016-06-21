(function($)
{
	$(document).ready(function(){
		
		var RC_rate_comments = new rate_comments();
	});
	
	function rate_comments()
	{
		var _config;
		var _loading_gif;
		var cookieTitle = 'RC_rate_comments';
		var self = this;
		var post_likes = [];
		var _liked_title = 'You recommended this comment';
		
		
		function _init()
		{
			_config = RC_config;
			_loading_gif = $('<img alt="....." src="'+_config.loading_url+'" />');
			_get_posts_likes();
		}
		
		function _get_posts_likes()
		{
			var post_id = parseInt(_config.post_id);
			if(post_id)
			{
				var data = xml_json_rpc_helper.xmlize_request(_config.get_post_likes_method, [_config.post_id]);
				
				jQuery.ajax(
						{
							url : _config.xml_rpc_url,
							type : "POST",
							contentType : "text/xml",
							processData : false,
							data : data,
							dataType : "json",
							success : _RC_get_likes_onSuccess
						}
				);
			}
			else
			{
				_RC_get_likes_onSuccess(false);
			}
		}
		
		function _RC_get_likes_onSuccess(response)
		{
			if( typeof response == 'object' )
			{
				for(index in response)
				{
					post_likes['cid_'+response[index].comment_id] = response[index].likes;
				}
			}
			_init_likes();
		}
		
		function _init_likes()
		{
			var RC_cookie = ( $.cookie(cookieTitle) === null ) ? {} : $.parseJSON($.cookie(cookieTitle));
			var RC_cookie_commentIDs = ( $.isArray(RC_cookie.cid) ) ? RC_cookie.cid : [];
			//disabling like buttons based on cookies set
			//binding click event
			$('.comments-like a').each(function(){
				var current_link = $(this);
				var current_comment_id = current_link.parent().attr('id').match(/.*like-([0-9]+).*/)[1];
				var comment_count = ( post_likes['cid_'+current_comment_id] != undefined) ? post_likes['cid_'+current_comment_id] : false;
				if( comment_count != false)
				{
					current_link.after('<span>'+comment_count+'</span>');
				}
				_is_comment_liked = ( $.inArray( parseInt(current_comment_id),RC_cookie_commentIDs ) !== -1 ) ? true : false;
				
				if( _is_comment_liked ) _set_link_liked(current_link);
				else current_link.removeAttr('class').click(_likeComment);
			});
		}
		
		function _likeComment(e)
		{
			e.preventDefault();
			var $like_link = $(e.target);
			if($like_link.hasClass('disabled') ) return;
			var comment_id = $like_link.parent().attr('id').match(/.*like-([0-9]+).*/)[1];
			var RC_cookie = ( $.cookie(cookieTitle) === null ) ? {} : $.parseJSON($.cookie(cookieTitle));
			var RC_cookie_commentIDs = ( $.isArray(RC_cookie.cid) ) ? RC_cookie.cid : [];
			_is_comment_liked = ( $.inArray( parseInt(comment_id),RC_cookie_commentIDs ) !== -1 ) ? true : false;
			if( _is_comment_liked ) return;
			$like_link.addClass('disabled').html('').append(_loading_gif);
			_update_likes(comment_id);
		};
		
		function _update_likes(comment_id)
		{
			var params = {comment_id: comment_id, post_id: _config.post_id};
			var data = xml_json_rpc_helper.xmlize_request(_config.update_likes_method, params);
			
			jQuery.ajax(
			    {
					url : _config.xml_rpc_url,
					type : "POST",
					contentType : "text/xml",
					processData : false,
					data : data,
					dataType : "json",
					success : _RC_like_onSuccess
			    }
			);
		};
		
		function _RC_like_onSuccess(response)
		{
			if(response.status == 'success')
			{
				_set_cookie(response.comment_id);
				var $like_container = $('#like-'+response.comment_id);
				var $like_link = $like_container.find('a');
				var likes = ($like_container.find('span').length != 0) ?  parseInt($like_container.find('span').text()) : 0;
				if(likes == 0)
				{
					$like_container.append( $('<span></span>') );
				}
				$like_container.find('span').text(likes+1);
				_set_link_liked($like_link); 
			}
			else
			{
				$like_link.html('Recommend').removeAttr('class');
			}
		};
		
		function _set_cookie(comment_id)
		{
			var RC_cookie = ( $.cookie(cookieTitle) === null ) ? {} : $.parseJSON($.cookie(cookieTitle));
			var RC_cookie_commentIDs = ( $.isArray(RC_cookie.cid) ) ? RC_cookie.cid : [];
			RC_cookie_commentIDs[RC_cookie_commentIDs.length] = comment_id;
			RC_cookie.cid = RC_cookie_commentIDs;
			//setting the cookie, changing the cookie object to string
			$.cookie(cookieTitle,JSON.stringify(RC_cookie),{ path: '/', expires: 365 });
		}
		
		function _set_link_liked( $liked_link )
		{
			$liked_link.html('Recommended').addClass('disabled').attr('title',_liked_title);
			$liked_link.unbind('click',_likeComment);
			$liked_link.click(function(e){e.preventDefault();});
		}
		
		_init();
	}
	
})(jQuery);