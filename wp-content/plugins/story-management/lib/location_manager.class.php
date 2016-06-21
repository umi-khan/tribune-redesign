<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SM_location_manager
{
	const META_KEY   = '_sm_location';

	private $_post_id;
	public  $locations;

	public function __construct($post_id, $load_locations = true)
	{
		$this->_post_id = $post_id;

		if( $load_locations ) $this->_load();
	}

	public function add($location)
	{
		if( false == $location ) return false;

		$location = trim( ucwords( $location ) );

		if( false == add_post_meta( $this->_post_id, self::META_KEY, $location ) ) return false;

		do_action( 'edit_post', $this->_post_id, get_post( $this->_post_id ) );

		return $location;
	}

	public function remove($location)
	{
		if( false == $location ) return false;

		if( false == delete_post_meta( $this->_post_id, self::META_KEY, $location ) ) return false;

		do_action( 'edit_post', $this->_post_id, get_post( $this->_post_id ) );

		return $location;
	}

	public static function get_locations()
	{
		global $wpdb;
		
		$sql = "
			SELECT DISTINCT meta_value as location
			FROM {$wpdb->postmeta}
			WHERE meta_key = '" . self::META_KEY . "' AND meta_value <> ''";

		$results = $wpdb->get_results( $sql );

		$locations = array();
		foreach( (array)$results as $result ) $locations[] = ucwords( $result->location );

		return $locations;
	}

	//////////// helper methods
	private function _load()
	{
		$this->locations = get_post_meta( $this->_post_id, self::META_KEY );
	}
}