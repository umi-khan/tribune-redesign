<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class SS_widget extends WP_Widget
{
	const DEFAULT_TITLE    = 'Recent Slideshows';
	const DEFAULT_CATEGORY = 0;
	const DEFAULT_LIMIT    = 4;
	const DEFAULT_ORDERBY  = 'recent';

	public static function register()
	{
		register_widget( __CLASS__ );
	}
	
	public function  __construct()
	{
		parent::__construct( 'ss-widget', __( 'Slideshows' ), array( 'description' => __( 'Displays slideshows by categories' ) ) );
	}
	
	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['title']    = $new_instance['title'];
		$instance['limit']    = (int) $new_instance['limit'];
		$instance['category'] = (int) $new_instance['category'];
		$instance['orderby']  = $new_instance['orderby'];

		$instance['limit']    = ( $instance['limit'] ) ? $instance['limit'] : self::DEFAULT_LIMIT;

		return $instance;
	}

	public function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array(
			'title' => __( self::DEFAULT_TITLE ),
			'category' => self::DEFAULT_CATEGORY,
			'limit' => self::DEFAULT_LIMIT,
			'orderby' => self::DEFAULT_ORDERBY ) );

		$title    = $instance['title'];
		$limit    = (int) $instance['limit'];
		$category = (int) $instance['category'];
		$orderby  = $instance['orderby'];

		$multimedia_category = get_category_by_slug('multimedia');		
		$categories = get_categories('parent=0&exclude='.$multimedia_category->cat_ID);

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' );?>">Title : </label>
			<input id="<?php echo $this->get_field_id( 'title' )?>" name="<?php echo $this->get_field_name( 'title' );?>"
					 type="text" value="<?php echo $title;?>" size="20" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'category' );?>">Show slideshows from category:</label>
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
			<label for="<?php echo $this->get_field_id( 'orderby' );?>">Order slideshows by:</label>
			<select id="<?php echo $this->get_field_id( 'orderby' );?>" name="<?php echo $this->get_field_name( 'orderby' );?>">
				<option value="recent" <?php selected( 'recent', $orderby ); ?>>Most Recent</option>
				<option value="viewed" <?php selected('viewed', $orderby); ?>>Most Viewed</option>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' );?>">Number of slideshows: </label>
			<input id="<?php echo $this->get_field_id( 'limit' )?>" name="<?php echo $this->get_field_name( 'limit' );?>"
					 type="text" value="<?php echo $limit;?>" size="2" />
		</p>
		
		<?php
	}

	public function widget($args,$instance)
	{
		extract($args);

		$title          = $instance['title'];
		$num_slideshows = (int) $instance['limit'];
		$orderby			 = $instance['orderby'];

		$category_id    = (int) $instance['category'];
		$category_id    = ( $category_id == -1 ) ? $this->_get_current_category() : $category_id;


		if( $orderby == 'viewed' ) $slideshows = SS_manager::get_mostviewed( $category_id, false, $num_slideshows );
		else                       $slideshows = SS_manager::get_latest( $category_id, 0, $num_slideshows );

		if ( empty($slideshows) ) return;
		
		$slideshow_category_id = get_category_by_slug( 'slideshows' )->cat_ID;
		if( $slideshow_category_id ) $slideshow_category_link = get_category_link( $slideshow_category_id );

		include SLIDESHOWS_PLUGIN_DIR . 'templates/widget.php';
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