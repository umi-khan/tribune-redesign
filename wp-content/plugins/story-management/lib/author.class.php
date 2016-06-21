<?php

class SM_author
{
	public $id;
	public $capabilities;
	public $first_name;
	public $last_name;
	public $nickname;
	public $nicename;
	public $name;

	// lazy load the following properties
	private $url;

	public function __construct($id=false, $capabilities=false, $first_name=false, $last_name=false, $nickname=false, $nicename=false)
	{
		$this->id           = (int)$id;
		$this->capabilities = (array)$capabilities;
		$this->first_name   = $first_name;
		$this->last_name    = $last_name;
		$this->nickname     = $nickname;
		$this->nicename     = $nicename;
		$this->name         = ucwords( $this->first_name . ' ' . $this->last_name );

		// lazy loaded fields
		$this->url = false;
	}

	public function __get($property)
	{
		if( $this->$property !== false ) return $this->$property;

		$method_name = '_get_' . $property;

		if( method_exists( $this, $method_name) ) return $this->$method_name();
	}

	public function insert()
	{
		if( $this->id ) return false;

		require_once ABSPATH . 'wp-includes/registration.php';

		$author_data = array(
			'user_pass'  => $this->_create_password(),
			'user_login' => $this->_create_username(),
			'first_name' => $this->first_name,
			'last_name'  => $this->last_name,
			'role'       => array_shift( array_keys( $this->capabilities, 1 ) )
		);

		if( false == defined( 'WP_IMPORTING' ) ) define( 'WP_IMPORTING', true );

		$author_id = wp_insert_user( $author_data );

		if( false == $author_id || is_wp_error( $author_id ) ) return false;

		$this->id = $author_id;

		return true;
	}

	public static function get_by_id( $author_id )
	{
		global $wpdb;
		
		$author_id = (int)$author_id;

		if( false == $author_id ) return false;

		$sql = "
			SELECT u.ID, u.user_nicename, ud.meta_key, ud.meta_value
			FROM {$wpdb->usermeta} AS ud
			     INNER JOIN {$wpdb->users} AS u ON ud.user_id = u.ID
			WHERE u.ID = {$author_id}";

		$results = $wpdb->get_results( $sql );

		$user_details = array();
		foreach( (array)$results as $result )
		{
			if( false == isset( $user_details['nicename'] ) ) $user_details['nicename'] = $result->user_nicename;
		
			$user_details[$result->meta_key] =  maybe_unserialize( $result->meta_value );
		}

		return new SM_author( $author_id, $user_details['wp_capabilities'], $user_details['first_name'], 
				  $user_details['last_name'], $user_details['nickname'], $user_details['nicename'] );
	}

	///////// lazy load helper functions
	private function _get_url()
	{
		$this->url = get_author_posts_url( $this->id, $this->nicename );
		
		return $this->url;
	}

	///////// helper functions
	private function _create_username()
	{
		return strtolower( str_replace( ' ' , '.', $this->name ) );
	}

	private function _create_password()
	{
		return wp_generate_password();
	}
}