<?php

/*
 * Class to Expose RPC methods of layout management
 */

class LM_rpc
{
	private $_current_user;

	public function __construct()
	{
		// need to setup the cookies array ourselves because wordpress unsets the cookies array on xmlrpc call
		$cookies = new LM_cookies();
		$cookies->init();
		
		$this->_current_user = new LM_user();
	}

	public function lm_update_story($story)
	{
		// check if the current user has the right credentials
		if(!$this->_current_user->is_editor())
		{
			error_log('LM_RPC: unauthenticated request to update story details from ip - ' .
					  $_SERVER['REMOTE_ADDR'] . ' on ' . date('Y-m-d H:i:s'));
			
			return HTML_JSON_response_helper::send_json(null, 'You are not authorized to perform this action');
		}

		// try to update the story
		if(LM_story::update_story($story['post_id'], $story['title'], $story['excerpt']))
		{
			$story_layouts = LM_layout::get_layout( $story['post_id'] );
			foreach( $story_layouts as $layout ) $this->_clear_cache( $layout['category_id'] );

			return HTML_JSON_response_helper::send_json('The story has been updated successfully.');
		}

		// we are here that means the story could not be updated so send an error response
		return HTML_JSON_response_helper::send_json(null, 'Arguments passed are not valid');
	}
	
	public function lm_update_layout($layout)
	{
		// check if the current user has the right credentials
		if(!$this->_current_user->is_editor())
		{
			error_log('LM_RPC: unauthenticated request to update story layout from ip - ' .
					  $_SERVER['REMOTE_ADDR'] . ' on ' . date('Y-m-d H:i:s'));

			return HTML_JSON_response_helper::send_json(null, 'You are not authorized to perform this action');
		}
		
		$category_id = $layout['category_id'];
		$group_id	 = $layout['group_id'];
		$old_story	 = $layout['old_story'];
		$new_story	 = $layout['new_story'];
		$do_cycle	 = $layout['do_cycle'];

		// if the arguments are not valid send an error response
		if(!is_numeric($category_id) || !is_numeric($group_id) || !is_numeric($new_story['id'])
				  || !is_numeric($old_story['position']))
		{
			return HTML_JSON_response_helper::send_json(null, 'Arguments passed are not valid');
		}

		if($this->_update_layout($new_story['id'], $category_id, $group_id, $old_story['position'], $do_cycle))
		{
			// invalidate the current page
			$this->_clear_cache( $category_id );

			// invalidate the home page
			$this->_clear_cache();

			return HTML_JSON_response_helper::send_json('The layout has been updated successfully.');
		}

		return HTML_JSON_response_helper::send_json(null, 'The story could not be inserted at position: ' . $old_story['position']);
	}

	// update the layout of the story
	private function _update_layout($story_id, $category_id, $group_id, $position, $do_cycle)
	{
		// instantiate the layout
		$layout = new LM_layout($category_id, $group_id, false);

		// if the posts have to be cycled
		if($do_cycle) return $layout->insert($story_id, $position);

		return $layout->replace($story_id, $position);
	}

	// invalidate the cache of the homepage and the page whose layout has to be updated and
	private function _clear_cache($category_id = 0)
	{
		$cache = LM_cache::get_cache_obj();

		if( false == ( $cache instanceof LM_cache ) ) return;

		$page_uri = ( $category_id == 0 ) ? get_home_url() : get_category_link( $category_id );

		// invalidate the current page
		$cache->invalidate_file( $page_uri );
	}
}