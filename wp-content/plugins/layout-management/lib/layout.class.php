<?php

/**
 * Description of layout_manager
 *
 * @author Express Media
 * @package Layout Manager
 */
class LM_layout
{
	/**
     * This property holds the wordpress database object
     */
    protected static $_db;

    /**
     * This property holds the name of the table that holds all the layout related information
     * @static
     */
    protected static $_tbl_layout;

    /**
     * This property holds the current category id. Category id identifies the current page.
     * For example Pakistan Page, Business Page, etc
     * @access Protected
     */
    protected $_category_id;

    /**
     * This property holds the current group id.
     * Group id groups stories that can be cycled through, swapped and replaced among themselves.
     * @access Protected
     */
    protected $_group_id;

    /**
     * This property holds all the LM_story objects in the form of stack. The post nearer to the top of the
     * stack have a higher position in the page.
     * @access Public
     */
    public $posts_stack;

	 public $template_stories;

	 protected $_is_manageable;

	/**
	* This is the constructor that sets the category_id and group_id and also loads all the posts in the layout.
	* There is a single instance for every combination of category_id and group_id
	* @access Public
	* @param object the wordpress database connectivity object
	* @param int $category_id
	* @param int $group_id
	*/
	public function __construct($category_id, $group_id = 0, $load_posts = true, $num_posts = false, $is_manageable = true)
	{
		self::_set_db();

		$this->_category_id	 = $category_id;
		$this->_group_id		 = $group_id;
		$this->_is_manageable = $is_manageable;

		if($load_posts)
		{
			if( false == $num_posts ) $num_posts = LM_config::get_instance()->get_num_group_stories( $category_id, $group_id );

			// load the posts and save them in the posts stack
			$this->_load($num_posts);
	
			// distribute the stories among templates
			$this->setup_stories_display();
		}
	}
	
	private static function _set_db()
	{
		if(!is_object(self::$_db))
		{
			global $wpdb;
			self::$_db = $wpdb;
			
			self::$_tbl_layout  = self::$_db->prefix . LM_config::TABLE_NAME;
		}
	}

	/**
	* This method loads all the posts required by the layout and sets up a stack of all the posts.
	* There is a single instance for every combination of category_id and group_id
	* @access Private
	* @return bool
	*/
	private function _load($num_posts = false)
	{
		$this->posts_stack = array();
		
		// get all posts from the db
		$load_posts_query = "SELECT layout.post_id, layout.position, posts.*
									 FROM " . self::$_tbl_layout . " AS layout
										 INNER JOIN " . self::$_db->posts . " AS posts ON posts.ID = layout.post_id AND posts.post_status = 'publish'
									 WHERE layout.category_id = %d AND layout.group_id = %d
									 ORDER BY layout.position";

		if($num_posts !== FALSE)
			$load_posts_query .= " LIMIT " . self::$_db->escape($num_posts);

		$posts = self::$_db->get_results(self::$_db->prepare($load_posts_query, $this->_category_id, $this->_group_id));
		foreach((array)$posts as $post_obj)
			$this->posts_stack[] = new LM_story($post_obj, $this->_category_id, $this->_group_id, $post_obj->position, $this->_is_manageable );
		
		return TRUE;
	}

	/**
	* This method inserts a post at the specified position.
	* The posts are cycled down from the position
	* where a new post has to be inserted.
	* @access Public
	* @param int $post_id the id of the post which is to be inserted
	* @param int $position the position where the post has to be inserted
	* @return bool
	*/
	public function insert($post_id, $position)
	{
		if( false == is_numeric( $post_id ) ) return false;
		
		$post_old_position = $this->get_post_position( $post_id );

		if ( $post_old_position == $position ) return;
		
		if ( $post_old_position !== null && false == $this->remove( $post_old_position ) ) return false;

		$this->lock_table();

		$table_name = self::$_tbl_layout;

		$update_positions_sql = "UPDATE $table_name SET `position` = `position` + 1 WHERE category_id = %d AND group_id = %d AND `position` >= %d ORDER BY `position` desc";
		$update_positions_sql = self::$_db->prepare( $update_positions_sql, $this->_category_id,	$this->_group_id,	$position );
		if( false === self::$_db->query( $update_positions_sql ) )
		{
			$this->unlock_table();
			return false;
		}

		$insert_sql = "INSERT INTO $table_name(post_id, category_id, group_id, `position`) VALUES (%d, %d, %d, %d)";
		$insert_sql = self::$_db->prepare( $insert_sql, $post_id, $this->_category_id, $this->_group_id, $position );
		if( false === self::$_db->query( $insert_sql ) )
		{
			$this->unlock_table();
			return false;
		}

		$this->unlock_table();

		// purge the old records
		$this->purge();

		return true;
	}

	/**
	* This method removes a post from the specified position.
	* The posts are cycled up from the position from which the post was removed.
	* @access Public
	* @param int $position the position from which the post has to be removed
	* @return bool
	*/
	public function remove($position)
	{
		$table_name = self::$_tbl_layout;

		$this->lock_table();

		$delete_sql = "DELETE FROM $table_name WHERE category_id = %d AND group_id = %d AND `position` = %d";
		$delete_sql = self::$_db->prepare( $delete_sql, $this->_category_id, $this->_group_id,	$position );
		if( false === self::$_db->query( $delete_sql ) )
		{
			$this->unlock_table();
			return false;
		}

		$update_sql = "UPDATE $table_name SET `position` = `position` - 1 WHERE category_id = %d AND group_id = %d AND `position` > %d ORDER BY `position` asc";
		$update_sql = self::$_db->prepare( $update_sql, $this->_category_id, $this->_group_id, $position );
		if( false === self::$_db->query( $update_sql ) )
		{
			$this->unlock_table();
			return false;
		}

		$this->unlock_table();

		return true;
	}

	/**
	* This method replaces a post in specified position with new_post_id.
	* If the new post already exists in the current category + group, then
	* it is removed and all posts below it are cycled up. Then the new post inserted
	* at specified position
	* @access Public
	* @param int $new_post_id the id of the post which is to be placed at the position
	* @param int $position the position at which the post has to be swapped
	* @return bool
	*/
	public function replace($new_post_id, $position)
	{		
		$post_old_position = $this->get_post_position( $new_post_id );
		if ( $post_old_position == $position ) return true;

		if ( $post_old_position !== NULL )
		{			
			if ( false === $this->remove( $post_old_position ) ) return false;
			
			if ( false === $this->insert( $new_post_id, $position ) ) return false;

			return true;
		}

		$table_name = self::$_tbl_layout;

		$this->lock_table();

		// replace post at $position with the $new_post_id
		$update_sql = "UPDATE $table_name SET post_id = %d WHERE category_id = %d AND group_id = %d AND `position` = %d";
		$update_sql = self::$_db->prepare( $update_sql , $new_post_id, $this->_category_id, $this->_group_id, $position );
		if( false === ( $num_rows_affected = self::$_db->query( $update_sql ) ) )
		{
			$this->unlock_table();
			return false;
		}

		// if no row was updated that means there is no post at position
		// then insert the $new_post_id at $position
		if( $num_rows_affected < 1 )
		{
			$insert_sql = "INSERT INTO $table_name (post_id, category_id, group_id, `position`) VALUES (%d, %d, %d, %d)";
			$insert_sql = self::$_db->prepare( $insert_sql, $new_post_id, $this->_category_id, $this->_group_id, $position );
			if( false === self::$_db->query( $insert_sql ) )
			{
				$this->unlock_table();
				return false;
			}
		}

		$this->unlock_table();

		return true;
	}

	public function purge()
	{
		$table_name = self::$_tbl_layout;

		$sql = "DELETE FROM $table_name WHERE category_id = %d AND group_id = %d AND `position` >= %d";
		$sql = self::$_db->prepare( $sql, $this->_category_id, $this->_group_id, LM_config::MAX_STORIES_PER_CATEGORY_GROUP );

		return self::$_db->query( $sql );
	}

	public function get_post_position($post_id)
	{
		// find the current position of post_id
		$sql = self::$_db->prepare(
						"SELECT `position`
						FROM " . self::$_tbl_layout . "
						WHERE post_id= %d
							AND category_id= %d
							AND group_id= %d",
						$post_id, $this->_category_id, $this->_group_id);

		$position = self::$_db->get_var($sql);
		return $position;
	}
	
	/**
	 * Sets up all the templates and the stories to be shown for each template
	 */
	public function setup_stories_display()
	{
		$templates   = LM_config::get_instance()->get_num_template_stories( $this->_category_id, $this->_group_id );
		$posts_stack = $this->posts_stack;

		$this->template_stories = array();
		foreach( (array)$templates as $template_id => $num_stories )
			$this->template_stories[$template_id] = (array)array_splice($posts_stack, 0, $num_stories, array());

		return true;
	}

	public function render_lm_template($template_id)
	{
		if(isset($this->template_stories[$template_id]))
		{
			$template_path = LM_config::get_instance()->get_template_path($template_id);

			if($template_path !== FALSE)
			{				
				$lm_stories = $this->template_stories[$template_id];
				
				include($template_path);
			}
		}
	}
	
	public static function get_layout($post_id)
	{
		self::_set_db();
		
		$layouts = array();
		
		if(is_numeric($post_id))
		{
			$get_post_layout_query = "SELECT category_id, group_id, `position`
												 FROM " . self::$_tbl_layout . "
												 WHERE post_id = %d
												 ORDER BY category_id, `position`";
			$post_layout = self::$_db->get_results(self::$_db->prepare($get_post_layout_query , $post_id));
			foreach((array)$post_layout as $layout)
			{
				$layouts[] = array(
					'category_id'	=> $layout->category_id,
					'group_id'		=> $layout->group_id,
					'position'		=> $layout->position,
					'template_id'	=> $layout->group_id . '-' . $layout->position
				);
			}
			
			return $layouts;
		}
	}

	public static function get_posts_by_position($category_id, $group_id, $position)
	{
		self::_set_db();
		
		// get all posts from the db
		$load_posts_query = "SELECT layout.post_id, layout.position, posts.*
									 FROM " . self::$_tbl_layout . " AS layout
										 INNER JOIN " . self::$_db->posts . " AS posts ON posts.ID = layout.post_id
									 WHERE layout.category_id = %d AND layout.group_id = %d AND layout.position = %d";

		$post_obj = self::$_db->get_row(self::$_db->prepare($load_posts_query, $category_id, $group_id, $position));

		return new LM_story($post_obj, $category_id, $group_id, $post_obj->position);
	}
	
	public function get_posts()
	{
		return $this->posts_stack;
	}

	protected function lock_table()
	{
		return self::$_db->query( 'LOCK TABLES ' . self::$_tbl_layout . ' WRITE' );
	}

	protected function unlock_table()
	{
		return self::$_db->query( 'UNLOCK TABLES' );
	}
}