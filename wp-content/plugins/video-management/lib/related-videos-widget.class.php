<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class VM_Related_Videos_Widget extends WP_Widget
{		
   
   const THUMBNAIL_WIDTH = 120;
   
	public static function register()
	{
		register_widget( __CLASS__ );
	}
	
	public function  __construct()
	{
		parent::__construct( 'vm-single-story-widget', __( 'Single Story Videos' ), array( 'description' => __( 'Displays Videos attached to the story, can be added on single story page only.' ) ) );
	}
	
	public function update($new_instance, $old_instance)
	{
		$instance          = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['thumbnail_width'] = (int)$new_instance['thumbnail_width'] == 0 ? self::THUMBNAIL_WIDTH :  (int)$new_instance['thumbnail_width'];      
      
		return $instance;
	}

	public function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'title' => __( 'Related Videos' ), 'thumbnail_width' => self::THUMBNAIL_WIDTH ) );

		$title           = $instance['title'];
      $thumbnail_width = $instance['thumbnail_width'];
      ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' );?>">Title : </label>
			<input id="<?php echo $this->get_field_id( 'title' )?>" name="<?php echo $this->get_field_name( 'title' );?>"
					 type="text" value="<?php echo $title;?>" size="20" />
		</p>
		
      <p>
			<label for="<?php echo $this->get_field_id( 'thumbnail_width' );?>">Thumbnail Width : </label>
			<input id="<?php echo $this->get_field_id( 'thumbnail_width' )?>" name="<?php echo $this->get_field_name( 'thumbnail_width' );?>"
					 type="text" value="<?php echo $thumbnail_width;?>" size="5" />
		</p>
      
		<?php
	}

	public function widget($args,$instance)
	{
		if( !is_single() ) return;
		
		global $post;		
		if(!isset($post->ID)) return;
			
		extract($args);				
		
		$post_videos       = new VM_manager($post->ID);
      
      $post_videos_count = count( $post_videos->videos );
		if( false == is_array( $post_videos->videos ) && $post_videos_count < 1 ) return;

      $thumbnail_width  = isset( $instance['thumbnail_width'] ) ? (int)$instance['thumbnail_width'] : self::THUMBNAIL_WIDTH;
      $thumbnail_height = $thumbnail_width * 0.75;
      
		$video_to_hide = null;
		if( class_exists('IM_Manager') )
		{			
			$image_manager = new IM_Manager( $post->ID, false );
			$has_images    = $image_manager->has_images();
			if( false === $has_images )
			{				
				if( $post_videos_count == 1 ) return;
				else $video_to_hide = $post_videos->default_video->id;
			}
		}

		$video_category_id = get_category_by_slug( 'videos' )->cat_ID;
		if( $video_category_id ) $video_category_link = get_category_link( $video_category_id );
      
      echo $before_widget;
      if ($instance['title']) echo $before_title . $instance['title'] . $after_title; 
		include VM_PLUGIN_DIR . 'templates/related-videos-widget.php';
      echo $after_widget;
	}
}