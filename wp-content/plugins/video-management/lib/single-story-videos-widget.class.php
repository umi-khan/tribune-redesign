<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class VM_single_story_video_widget extends WP_Widget
{
	const DEFAULT_TITLE = 'Related Video';
	
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
		
		return $instance;
	}

	public function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'title' => __( self::DEFAULT_TITLE ) ) );

		$title    = $instance['title'];
		
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' );?>">Title : </label>
			<input id="<?php echo $this->get_field_id( 'title' )?>" name="<?php echo $this->get_field_name( 'title' );?>"
					 type="text" value="<?php echo $title;?>" size="20" />
		</p>
		
		<?php
	}

	public function widget($args,$instance)
	{
		if( !is_single() ) return;
		
		global $post;		
		if(!isset($post->ID)) return;
			
		extract($args);		
		$title = $instance['title'];		
		
		$post_videos       = new VM_manager($post->ID);
		$post_videos_count = count( $post_videos->videos );
		if( false == is_array( $post_videos->videos ) && $post_videos_count < 1 ) return;

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

		include VM_PLUGIN_DIR . 'templates/single-story-videos-widget.php';
	}
}