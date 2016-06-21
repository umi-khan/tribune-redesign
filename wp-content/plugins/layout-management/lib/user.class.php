<?php

/**
 * A class that manages the user authentication related suff
 *
 * @author ovais.tariq
 */
class LM_user
{
	private $_user_id;
	private $_user_data;

	public function  __construct()
	{
		$this->_user_id	= false;
		$this->_user_data = false;

		$this->_set_current_user();
	}

	public function __get($property)
	{
		if(property_exists($this->_user_data, $property))
			return $this->_user_data->$property;
	}

   public function authenticate()
	{
		return ($this->_user_id && $this->_user_data);
	}

	/**
	*  the currently logged in user is editor or administrator
	* # User Level 5 converts to Editor
	* # User Level 6 converts to Editor
	* # User Level 7 converts to Editor
	* # User Level 8 converts to Administrator
	* # User Level 9 converts to Administrator
	* # User Level 10 converts to Administrator
	*/
	public function is_editor()
	{
		return ( $this->_user_data && ( isset($this->_user_data->user_level) && $this->_user_data->user_level >= 5) );
	}

	private function _set_current_user()
	{
		$user_id			= false;
		$current_user	= false;

		// try to get the user from the wordpress global variable
		$current_user = wp_get_current_user();

		// if the current user could not be retrieved, try to retrieve from cookie
		if(!$current_user)
		{
			if(!($user_id = $this->_get_userid_from_cookie()))
				return false;

			$current_user = get_userdata($user_id);
		}

		$this->_user_id	= ($user_id) ? $user_id : $current_user->ID;
		$this->_user_data = $current_user;

		return true;
	}

	private function _get_userid_from_cookie()
	{
		if(empty($_COOKIE[LOGGED_IN_COOKIE])
				  || !$user_id = wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in'))
		{
		 	return false;
		}

		return $user_id;
	}
}