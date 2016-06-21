<?php

/**
 * @author ovais.tariq
 */
class ET_trends
{
	const TOP_STORIES_COUNT = 1;
	const SUB_STORIES_COUNT = 14;

	const LAYOUT_CATEGORY = LM_config::CATEGORY_HOME_ID;
	const LAYOUT_GROUP    = LM_config::GROUP_MAIN_STORIES;

	// the view which is queried to fetch the stories in a trend
	const TAG_POSTS_VIEW = 'tag_posts';	

	public $trend_name;
	public $trend_id;
	
	private $_trend_slug;
	private $_top_template_count;
	private $_sub_template_count;

	/**
	 * Sets up the path for all the templates.
	 * It first tries to check if a folder named <strong>et-templates</strong> exists. If it does exist then the templates
	 * present in that folder are used, otherwise the templates from this plugin's folders are used.
	 * @access private
	 * @static
	 * @return array An array of template paths.
	 */
	private static function _fetch_template_paths( $slug = null )
	{            
      static $template_paths = false;

		if( false == $template_paths )
		{
         if( is_null( $slug ) )
         {
            global $wp_query;         
            $slug = $wp_query->get_queried_object()->slug;
         }         
			$template_dir = 'trends-templates';

			$templates = array(
				'top'       => 'top-story.php',
				'sub'       => 'sub-story.php',
				'more_link' => 'more_link.php'
			);

         foreach( $templates as $tpl_id => $tpl_name )
         {
            if(! is_null( $slug ) ) $template_file      = TEMPLATEPATH . DIRECTORY_SEPARATOR . $template_dir . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . $tpl_name;
            $template_slug_file = TEMPLATEPATH . DIRECTORY_SEPARATOR . $template_dir . DIRECTORY_SEPARATOR . $tpl_name;
                        
            if( file_exists( $template_file )) $template = $template_file;            
            elseif( file_exists( $template_slug_file ) ) $template = $template_slug_file;            
            else $template = ET_PLUGIN_DIR . 'templates'. DIRECTORY_SEPARATOR. $tpl_name;
            
            $template_paths[$tpl_id] = $template;
         }			
		}
      
		return $template_paths;
	}
	
	/**
	 * Shows which trends, the posts recently posted in have been attached to
	 * @param int $num_trends The number of trends to be fetched
	 * @uses wpdb The wordpress db class is used to query the view to fetch trends
	 * @uses stdClass Trend objects are created as objects of this class
	 * @return array An array of stdClass objects which contain trend details, and is in the following format:
	 * <br />$trend       = new stdClass();
	 *	<br />$trend->id   = trend_id;
	 *	<br />$trend->name = trend_name;
	 *	<br />$trend->url  = get_home_url() . '/' . trend_slug;
	 */
	public static function latest_trends($num_trends = 5, $duration_in_days = 10 )
	{
		global $wpdb;
		
      $duration_in_days =  (int)$duration_in_days < 1 ? 10 :  (int)$duration_in_days;
		$num_trends       = ( (int)$num_trends < 1 ) ? 5 : (int)$num_trends;

		$sql = "SELECT term_id, `name`, slug FROM ( SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = 'post' AND post_status = 'publish' AND post_date > ( NOW() - INTERVAL ".$duration_in_days." DAY ) ) AS p 
		INNER JOIN ".$wpdb->prefix."term_relationships AS tr ON  p.ID = tr.object_id
		INNER JOIN ".$wpdb->prefix."term_taxonomy AS tt using(term_taxonomy_id)
		INNER JOIN ".$wpdb->prefix."terms         AS t  USING(term_id) 
		WHERE tt.taxonomy = 'post_tag'
		GROUP BY t.term_id HAVING COUNT(ID) > 0
		ORDER BY COUNT(ID) desc
		LIMIT $num_trends";
      
		$results  = $wpdb->get_results( $sql );

		$site_url = trailingslashit( get_home_url() );

		$trends = array();
		foreach( (array)$results as $res )
		{
			$trend       = new stdClass();
			$trend->id   = $res->term_id;
			$trend->name = $res->name;
			$trend->url  = trailingslashit( $site_url . $res->slug );

			$trends[] = $trend;
		}

		return $trends;
	}

	public function __construct($trend_slug, $top_template_count = self::TOP_STORIES_COUNT, $sub_template_count = self::SUB_STORIES_COUNT)
	{
		$this->_trend_slug           = $trend_slug;

		$this->_top_template_count = (int)$top_template_count;
		$this->_sub_template_count = (int)$sub_template_count;

		$this->trend_id            = false;
		$this->trend_name          = false;
	}

   public static function trend_slug() {
      return self::$_trend_slug;
   }
	/**
	 * This is the main function which fetches records to be displayed, sets up the template path and then displayes the
	 * stories according to the number of stories to be displayed for each template.
	 * @param int $offset The offset from which to start fetching the records
	 * @param string Empty string on error, html on success.
	 */
	public function fetch_page($offset = 0)
	{
		// setup details: trend_id, trend_name, the stories, stories_count, and whether there are more stories in the trend
		extract( $this->_fetch_details( $offset ) );
		$this->trend_id   = $trend_id;
		$this->trend_name = $trend_name;

		if( $stories_count < 1 ) return false;

		// setup the template paths
		$template_paths     = self::_fetch_template_paths( $this->_trend_slug );
		$template_top       = $template_paths['top'];
		$template_sub       = $template_paths['sub'];
		$template_more_link = $template_paths['more_link'];

		$html = '';

		ob_start();
		
		// render all the stories
		for( $i = 0; $i < $stories_count; $i++ )
		{
			// set which template to use
			$template_path = ( $i < $this->_top_template_count ) ? $template_top : $template_sub;

			// setup the story details
			$story         = $stories[$i];
			$image_manager = new IM_Manager( $story->id, false );
			$image         = $image_manager->default_image;         
			$video         = false;
         $has_images    = $image_manager->has_images();
			if( false === $has_images )
			{
				$video_manager = new VM_Manager( $story->id, false );
				$video         = $video_manager->default_video;
			}
			$author        = SM_author_manager::get_author_posts_link( $story->id );

			include $template_path;
		}

		// render the more link
		if( $has_more_stories )
		{
			$trend_slug  = $this->_trend_slug;
			$next_offset = $offset + $stories_count;

			include $template_more_link;
		}

		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Fetches the trend details (trendid, trend name), the stories in each trend, count of stories and a flag that sets whether more stories are available.
	 * @access private
	 * @uses LM_story Creates LM_story object for each story in the current trend
	 * @uses LM_config Uses the layout_category and layout_group constants to identify each story, these are needed by
	 * the LM_story object
	 * @uses wpdb The wordpress db class is being used to query the table
	 * @param int $offset The offset from which to fetch the posts from
	 * @return array An array of $trend_id, $trend_name, $stories, $stories_count, $has_more_stories
	 */
	private function _fetch_details($offset)
	{	
		// initialize the details
		$trend_id         = false;
		$trend_name       = false;
		$stories          = array();
		$stories_count    = 0;
		$has_more_stories = false;
		$position         = $offset;
		$num_records      = $this->_top_template_count + $this->_sub_template_count;

		// fetch the stories
		$results = $this->_fetch_stories( (int)$offset, (int)$num_records + 1 );
		
		if( count( $results ) < 1 ) return compact( $trend_id, $trend_name, $stories, $stories_count, $has_more_stories );

		// if the number of stories fetched is one more than $num_records then set the _has_more_stories flag to true
		if( count( $results ) > $num_records )
		{
			$has_more_stories = true;
			array_pop( $results );
		}

		// set the number of stories
		$stories_count = count( $results );

		// populate the stories array
		foreach( (array)$results as $post )
		{
			$stories[] = new LM_story( $post, self::LAYOUT_CATEGORY, self::LAYOUT_GROUP, $position, false );

			if( false == $trend_id || false == $trend_name )
			{
            $trend = get_term_by('slug', $this->_trend_slug, 'post_tag');
				$trend_id   = $trend->term_id;
				$trend_name = ucwords( $trend->name );
			}

			$position++;
		}
		
		return compact( 'trend_id', 'trend_name', 'stories', 'stories_count', 'has_more_stories' );
	}

	/**
	 * This function queries the db and fetches the records.
	 * @access private
	 * @global wpdb $wpdb An object of wordpress db class
	 * @param int $offset The number of records to offset from
	 * @param int $num_records The number of records to fetch
	 * @return array Returns an array of post details objects
	 */
	private function _fetch_stories($offset, $num_records)
	{
		global $wpdb;
		
		$tag_slug = $wpdb->escape( $this->_trend_slug );
      
      $args = array(
         'offset'      => $offset,
         'numberposts' => $num_records,
         'tax_query' => array(
             array(
                 'taxonomy' => 'post_tag',
                 'field'    => 'slug',
                 'terms'    => $tag_slug
             )
         )
     );
     return get_posts($args);
	}
}