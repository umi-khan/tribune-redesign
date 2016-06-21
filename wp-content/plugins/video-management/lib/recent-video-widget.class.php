<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class VM_recent_video_widget extends WP_Widget
{
	const DEFAULT_TITLE    = 'Recent Videos';
	const DEFAULT_CATEGORY = 0;
	const DEFAULT_LIMIT    = 4;

	public static function register()
	{
		register_widget( __CLASS__ );
	}
	
	public function  __construct()
	{
		parent::__construct( 'vm-widget', __( 'Videos' ), array( 'description' => __( 'Display recently added videos' ) ) );
	}
	
	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['title']    = $new_instance['title'];
		$instance['limit']    = (int) $new_instance['limit'];
		$instance['category'] = (int) $new_instance['category'];

		$instance['limit']    = ( $instance['limit'] ) ? $instance['limit'] : self::DEFAULT_LIMIT;

		return $instance;
	}

	public function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array(
			'title' => __( self::DEFAULT_TITLE ), 'category' => self::DEFAULT_CATEGORY, 'limit' => self::DEFAULT_LIMIT ) );

		$title    = $instance['title'];
		$limit    = (int) $instance['limit'];
		$category = (int) $instance['category'];

		$categories = get_categories('parent=0');

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' );?>">Title : </label>
			<input id="<?php echo $this->get_field_id( 'title' )?>" name="<?php echo $this->get_field_name( 'title' );?>"
					 type="text" value="<?php echo $title;?>" size="20" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'category' );?>">Show videos from category:</label>
			<select id="<?php echo $this->get_field_id( 'category' );?>" name="<?php echo $this->get_field_name( 'category' );?>">
				<option value="0" <?php selected( '0', $category ); ?>>All</option>
				<option value="-1" <?php selected('-1', $category); ?>>Category's Default</option>
				<?php foreach( $categories AS $cat ) : ?>
				<option value="<?php echo $cat->cat_ID;?>" <?php selected( $cat->cat_ID, $category ); ?>>
					<?php echo $cat->name;?>
				</option>
				<?php endforeach;?>
				
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' );?>">Number of videos: </label>
			<input id="<?php echo $this->get_field_id( 'limit' )?>" name="<?php echo $this->get_field_name( 'limit' );?>"
					 type="text" value="<?php echo $limit;?>" size="2" />
		</p>
		
		<?php
	}

	public function widget($args,$instance)
	{
		extract($args);

		$title       = $instance['title'];
		$category_id = (int) $instance['category'];
		$num_videos  = (int) $instance['limit'];

		$category_id = ( $category_id == -1 ) ? $this->_get_current_category() : $category_id;

		$videos = VM_manager::get_latest_by_category( $category_id, 0, $num_videos );

		$video_category_id = get_category_by_slug( 'videos' )->cat_ID;
		if( $video_category_id ) $video_category_link = get_category_link( $video_category_id );

		include VM_PLUGIN_DIR . 'templates/recent-video-widget.php';
	}

	private function _get_current_category()
	{
		if( substr($_SERVER['REQUEST_URI'], 1 ) == "" ) return 0;

		$query = explode( '/', substr( $_SERVER['REQUEST_URI'], 1 ) ); // ignore first '/'

		for( $i = count( $query ) - 1; $i >= 0; $i--)
		{
			if( isset( $query[$i] ) && $query[$i] != '' )
			{
				if( $id_obj = get_category_by_slug( $query[$i] ) ) return $id_obj->term_id;
			}
		}

		return false;
	}
}