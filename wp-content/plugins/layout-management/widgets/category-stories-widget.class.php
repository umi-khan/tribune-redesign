<?php

add_action('widgets_init', array('LM_category_stories_widget', 'init'));

class LM_category_stories_widget extends WP_Widget
{
	const NUM_STORIES = 5;
	const DEFAULT_CATEGORY_ID	=	1;
	
	private $template_name;

	public static function init()
	{
		register_widget('LM_category_stories_widget');
	}
	
	public function __construct()
	{
		$this->template_name	=	LM_config::WIDGETS_TEMPLATES_DIR.'/category-stories-widget.php';
		$widget_options = array(
			'description' => __('Display top stories of selected Sections')
		);
		
		parent::__construct('LM_Category_Stories', __('Layout Management Category Stories'), $widget_options);
	}

	// When Widget Control Form Is Posted
	function update($new_instance, $old_instance)
	{
      $instance = $old_instance;
		$instance['category'] = (int) $new_instance['category'];
		
		return $instance;
	}

	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'category' => self::DEFAULT_CATEGORY_ID ) );
		$category = (int) $instance['category'];
		
		$categories = get_categories();
		
		?>
			<label for="<?php echo $this->get_field_id( 'category' );?>">Show Posts from category:</label>
			<select id="<?php echo $this->get_field_id( 'category' );?>" name="<?php echo $this->get_field_name( 'category' );?>">
				<?php foreach( $categories as $cat ) : ?>
				<option value="<?php echo $cat->cat_ID;?>" <?php selected( $cat->cat_ID, $category ); ?>>
					<?php echo $cat->name;?>
				</option>
				<?php endforeach;?>
			</select>
			
		<?php
   }

	function widget($args,$instance)
	{
		extract($args);
		
		$category_id = $instance['category']; 
		$category = $this->_get_category($category_id);
	?>
		<div class="category-stories-widget widget <?php echo $category->slug;?>">
			<h4 class="title"><a href="<?php echo $category->url;?>"><?php echo $category->name; ?></a></h4>
			<div class="content clearfix">
				<?php
				$cat_layout = new LM_layout($category->cat_ID, LM_config::GROUP_MAIN_STORIES, true, self::NUM_STORIES, false);
				$lm_posts   = $cat_layout->get_posts();
				$this->render($lm_posts);
				?>
			</div>
		</div>
	<?php
	}
	
	private function render($lm_stories)
	{
		$template_path = (file_exists(TEMPLATEPATH . '/' . $this->template_name)) ?
										TEMPLATEPATH . '/' . $this->template_name :
										LAYOUT_MANAGEMENT_PLUGIN_DIR . $this->template_name;
		
		require($template_path);
	}

	private function _get_category($category_id)
	{
		global $wpdb;

		$category_id = (int)$category_id;
		$site_url    = get_bloginfo("url") . '/';

		// setup the default category object defaulting to the home page category
		$category_obj         = new stdClass();
		$category_obj->cat_ID = $category_id;
		$category_obj->name   = 'Home';
		$category_obj->slug   = 'home';
		$category_obj->url    = $site_url;

		if( $category_id < 1 ) return $category_obj;

		$sql = "
			SELECT tt.term_id, t1.name, t1.slug, t2.slug AS parent_slug
			FROM {$wpdb->term_taxonomy} AS tt
				INNER JOIN {$wpdb->terms} AS t1 ON t1.term_id = tt.term_id
				LEFT JOIN {$wpdb->terms} AS t2 ON tt.parent = t2.term_id
			WHERE tt.term_id = $category_id
			AND tt.taxonomy = 'category'";

		$category = $wpdb->get_row( $sql );

		if( false == $category ) return $category_obj;

		// setup the category name
		$category_obj->name = esc_html( $category->name );

		// setup the category slug
		$category_obj->slug = esc_html( $category->slug );

		// setup the category url, if this is a sub-category also include the parent category slug
		$category_obj->url .= ( ( $category->parent_slug ) ? $category->parent_slug . '/' : '' ) . $category->slug . '/';

		return $category_obj;
	}
}