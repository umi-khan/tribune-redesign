<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SM_author_manager
{
	const META_KEY = '_sm_author';

	private $_post_id;
	public  $authors;

	public function __construct($post_id, $load_authors = true)
	{
		$this->_post_id = $post_id;

		if( $load_authors ) $this->_load();
	}

	public function add($author_or_id)
	{
		if( false == $author_or_id ) return false;

		if( is_numeric( $author_or_id ) )
		{
			$author = SM_author::get_by_id( $author_or_id );
		}
		else
		{
			$author_name = ucwords( $author_or_id );
			$author_name = explode( ' ', $author_name );
			$author_name = array_map( 'trim', $author_name );

			$first_name   = array_shift( $author_name );
			$last_name    = implode( ' ', $author_name );
			$capabilities = array( 'contributor' => 1 );

			$author = new SM_author( false, $capabilities, $first_name, $last_name );
			
			if( false == $author->insert() ) $author = false;
		}

		if( false == ( $author instanceof SM_author ) ) return false;

		if( false == add_post_meta( $this->_post_id, self::META_KEY, $author->id ) ) return false;

		do_action( 'edit_post', $this->_post_id, get_post( $this->_post_id ) );

		return $author;
	}

	public function remove($author_id)
	{
		if( false == $author_id ) return false;

		$author = SM_author::get_by_id( $author_id );

		if( false == ( $author instanceof SM_author ) ) return false;

		if( false == delete_post_meta( $this->_post_id, self::META_KEY, $author->id ) ) return false;

		do_action( 'edit_post', $this->_post_id, get_post( $this->_post_id ) );

		return $author;
	}

	public static function get_authors_by_post( $post_id , $post_meta_key )
	{
		global $wpdb;

		$sql = "
			SELECT u.ID, u.user_nicename, ud.meta_key, ud.meta_value
			FROM {$wpdb->usermeta} AS ud
			     INNER JOIN {$wpdb->users} AS u ON ud.user_id = u.ID
				  INNER JOIN {$wpdb->usermeta} AS um ON u.ID = um.user_id
				  INNER JOIN {$wpdb->postmeta} AS pm ON pm.meta_value = u.ID
			WHERE um.meta_key = 'wp_capabilities'
			AND ( um.meta_value LIKE '%contributor%'
				  OR um.meta_value LIKE '%author%'
				  OR um.meta_value LIKE '%editor%'
				  OR um.meta_value LIKE '%administrator%' )
				  AND pm.meta_key = '".$post_meta_key."'
				  AND pm.post_id = ".$post_id;

		$sql .= " ORDER BY pm.meta_id ASC ";

		$results = $wpdb->get_results( $sql );

		$users = array();
		foreach( (array)$results as $result )
		{
			if( false == isset( $users[$result->ID] ) || false == is_array( $users[$result->ID] ) )
				$users[$result->ID] = array();

			if( false == isset( $users[$result->ID]['nicename'] ) ) $users[$result->ID]['nicename'] = $result->user_nicename;

			$users[$result->ID][$result->meta_key] =  maybe_unserialize( $result->meta_value );
		}

		$authors = array();
		foreach( (array)$users as $user_id => $user_details )
		{
			$authors[] = new SM_author( $user_id, $user_details['wp_capabilities'], $user_details['first_name'],
					  (isset($user_details['last_name']) ? $user_details['last_name'] : ''), $user_details['nickname'], $user_details['nicename'] );
		}

		return $authors;
	}

	public static function get_authors($author_ids = array())
	{
		global $wpdb;

		$author_ids = (array)$author_ids;
		
		$sql = "
			SELECT u.ID, u.user_nicename, ud.meta_key, ud.meta_value
			FROM {$wpdb->usermeta} AS ud
			     INNER JOIN {$wpdb->users} AS u ON ud.user_id = u.ID
				  INNER JOIN {$wpdb->usermeta} AS um ON u.ID = um.user_id
			WHERE um.meta_key = 'wp_capabilities'
			AND ( um.meta_value LIKE '%contributor%'
				  OR um.meta_value LIKE '%author%'
				  OR um.meta_value LIKE '%editor%'
				  OR um.meta_value LIKE '%administrator%' )";

		if( count( $author_ids ) > 0 ) $sql .= " AND u.ID in ( " . implode( ',', $author_ids ) . " )";

		$sql .= " ORDER BY u.user_nicename ASC ";
		
		$results = $wpdb->get_results( $sql );

		$users = array();
		foreach( (array)$results as $result )
		{
			if( false == isset( $users[$result->ID] ) || false == is_array( $users[$result->ID] ) )
				$users[$result->ID] = array();

			if( false == isset( $users[$result->ID]['nicename'] ) ) $users[$result->ID]['nicename'] = $result->user_nicename;

			$users[$result->ID][$result->meta_key] =  maybe_unserialize( $result->meta_value );
		}

		$authors = array();
		foreach( (array)$users as $user_id => $user_details )
		{
			$authors[] = new SM_author( $user_id, $user_details['wp_capabilities'], $user_details['first_name'],
					  (isset($user_details['last_name']) ? $user_details['last_name'] : ''), $user_details['nickname'], $user_details['nicename'] );
		}

		return $authors;
	}

	public static function get_authors_with_posts($name_starts_with = false)
	{
		global $wpdb;

		$name_starts_with = trim( $name_starts_with );

		$sql = "
			SELECT pm.author_id, pm.num_posts, u.user_nicename, ud.meta_key, ud.meta_value
			FROM (
					SELECT m.meta_value AS author_id, COUNT(m.post_id) AS num_posts
					FROM {$wpdb->postmeta} AS m WHERE m.meta_key = '" . self::META_KEY . "' GROUP BY m.meta_value
					) AS pm
			INNER JOIN {$wpdb->users} AS u ON pm.author_id = u.ID
			INNER JOIN {$wpdb->usermeta} AS ud ON ud.user_id = u.ID";

		if( $name_starts_with && strlen( $name_starts_with ) > 0 )
		{
			$name_starts_with = (string)$wpdb->escape( $name_starts_with );
			
			$sql .= "
				INNER JOIN {$wpdb->usermeta} AS um ON um.user_id = ud.user_id
				WHERE um.meta_key = 'first_name' AND um.meta_value LIKE '{$name_starts_with}%'";
		}

		$sql .= "
			ORDER BY u.user_nicename ASC";

		$results = $wpdb->get_results( $sql );

		$users = array();
		foreach( (array)$results as $result )
		{
			if( false == is_array( $users[$result->author_id] ) ) $users[$result->author_id] = array();

			if( false == isset( $users[$result->author_id]['nicename'] ) )
				$users[$result->author_id]['nicename'] = $result->user_nicename;

			if( false == isset( $users[$result->author_id]['num_posts'] ) )
				$users[$result->author_id]['num_posts'] = $result->num_posts;

			$users[$result->author_id][$result->meta_key] =  maybe_unserialize( $result->meta_value );
		}

		$authors = array();
		foreach( (array)$users as $user_id => $user_details )
		{
			$authors[] = array(
				'data'      => new SM_author( $user_id, $user_details['wp_capabilities'], $user_details['first_name'],
					                    $user_details['last_name'], $user_details['nickname'], $user_details['nicename'] ),
				'num_posts' => $user_details['num_posts']
			);
		}

		return $authors;
	}

	public static function list_authors($name_starts_with = false)
	{
		$authors_with_posts = self::get_authors_with_posts( $name_starts_with );

		$authors_links = array();
		foreach( $authors_with_posts as $author )
		{
			$authors_links[] = '
				<li>
					<a href="' . $author['data']->url . '" title="' . esc_attr( "Stories by " . $author['data']->name ) . '">' . 
						$author['data']->name . '
					</a>
					('. $author['num_posts'] . ')
				</li>';
		}

		if( count( $authors_links ) > 0 ) echo implode( ' ', $authors_links );
		else                              echo '<li>No author.</li>';

		return true;
	}

	public static function get_author_posts_link($post_id)
	{
		$post_authors = new self( $post_id );
		
		$authors_links = array();
		foreach( (array)$post_authors->authors as $a )
		{
			if( is_array( $post_authors->authors ) && count( $post_authors->authors ) == 1 &&
					  preg_match( "/\bexpress\b/i", $a->name ) ) continue;

			$authors_links[] = '<a href="'.$a->url.'" title="'.esc_attr( "Posts by " . $a->name ).'">'.$a->name.'</a>';
		}

		return implode( ' / ', $authors_links );
	}

	//////////// helper methods
	private function _load()
	{
		$author_ids = get_post_meta( $this->_post_id, self::META_KEY );

		$this->authors = array();

		//if( is_array( $author_ids ) && count( $author_ids ) > 0 ) $this->authors = self::get_authors( $author_ids );
		if( is_array( $author_ids ) && count( $author_ids ) > 0 ) 
			$this->authors = self::get_authors_by_post( $this->_post_id, self::META_KEY );
	}
}