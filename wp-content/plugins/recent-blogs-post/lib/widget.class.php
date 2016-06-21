<?php


class Recent_Blogs_Widget extends WP_Widget
{
	const DEFAULT_LIMIT      = 5;
	const DEFAULT_TITLE      = 'Recent Blogs';
	const DEFAULT_CACHE_TIME = 30; //time in minutes
	const DEFAULT_FEED_URL   = 'http://blogs.tribune.com.pk/feed/';
	
	public static function register()
	{
		register_widget( __CLASS__ );
	}

	function  __construct()
	{
		parent::__construct( 'Recent_Blogs_Widget', __( 'Recent Blogs Widget' ), array( 'description' => __('Shows latest from Tribune blogs' ) ) );
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$cache_time = intval($new_instance['cache_time']);
		if( $cache_time <= 0 ) $new_instance['cache_time'] = self::DEFAULT_CACHE_TIME;

		$limit = intval($new_instance['limit']);
		if( $limit <= 0 ) $new_instance['limit'] = self::DEFAULT_LIMIT;

		$new_instance['feed_url'] = $new_instance['feed_url'];

		$instance['limit']        = $new_instance['limit'];
		$instance['title']        = $new_instance['title'];
		$instance['feed_url']     = $new_instance['feed_url'];
		$instance['cache_time']   = $new_instance['cache_time'];

		return $instance;
	}

	function form($instance)
	{
		global $wpdb;

		$defaults = array(
			'limit'      => self::DEFAULT_LIMIT,
			'title'      => __(self::DEFAULT_TITLE),
			'feed_url'   => self::DEFAULT_FEED_URL,
			'cache_time' => self::DEFAULT_CACHE_TIME
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$limit        = $instance['limit'];
		$title = $instance['title'];
		$feed_url     = $instance['feed_url'];
		$cache_time   = $instance['cache_time'];

	?>
	<p>
		 <label for="<?php echo $this->get_field_id('title');?>">Title: </label>
		 <input id="<?php echo $this->get_field_id('title')?>" name="<?php echo $this->get_field_name('title');?>"
				  type="text" value="<?php echo $title;?>"/><br/>

		 <label for="<?php echo $this->get_field_id('limit');?>">No. of posts: </label>
		 <input id="<?php echo $this->get_field_id('limit')?>" name="<?php echo $this->get_field_name('limit');?>"
				  type="text" value="<?php echo $limit;?>" size="4" /><br/>

		 <label for=<?php echo $this->get_field_id('feed_url'); ?>>Feed url: </label>
		 <input id=<?php echo $this->get_field_id('feed_url'); ?> name=<?php echo $this->get_field_name('feed_url'); ?> type="text" value=<?php echo $feed_url; ?>/><br/>
		 
		 <label for="<?php echo $this->get_field_id('cache_time');?>">Cache Time in minutes</label>
		 <input id="<?php echo $this->get_field_id('cache_time')?>" name="<?php echo $this->get_field_name('cache_time');?>"
				  type="text" value="<?php echo $cache_time;?>" size="4" />
	</p>
<?php
	}

	function widget( $args , $instance )
	{		
		extract($args);		

		$limit        = $instance['limit'];
		$widget_title = $instance['title'];
		$feed_url     = $instance['feed_url'];
		$cache_time   = $instance['cache_time'];

		$manager = new Blog_Posts_Manager( $feed_url, $cache_time, $limit );

		$data = $manager->get();

		$feed_site_title = $data['feeds_site_title'];
		$feeds_site_url  = $data['feeds_site_url'];
		$posts           = $data['posts'];

		require_once( RECENTBLOGS_PLUGIN_DIR . 'templates/widget.php' );
	}
}
