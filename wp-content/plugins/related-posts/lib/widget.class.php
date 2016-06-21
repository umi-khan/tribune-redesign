<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

add_action( 'widgets_init', 'ERP_widget::register' );

class ERP_widget extends WP_Widget
{
	public function __construct()
	{
		parent::__construct( 'ERP_widget', __( 'Express Related Posts'), array( 'description' => __( 'Shows related posts' ) ) );
	}

	public static function register()
	{
		register_widget( __CLASS__ );
	}

	public function update($new_instance, $old_instance)
	{
		$instance          = $old_instance;
		$instance['limit'] = (int)$new_instance['limit'];
		
		return $instance;
	}

	public function form($instance)
	{
		$instance = wp_parse_args( (array)$instance, array( 'limit' => 5 ) );
		$limit    = (int)$instance['limit'];

		?>

		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>">Limit:</label>
			<input id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>"
					 type="text" value="<?php echo $limit; ?>" size="2" />
		</p>

		<?php
	}

	function widget($args,$instance)
	{
		global $post;

		extract($args);

		$post_id	= $post->ID;
		$limit   = (int)$instance['limit'];
		$related_posts_html = the_erp_related_posts( $post_id, $limit, false, false );
		
		if(!$related_posts_html) return;
		?>

		<div class="related-stories-widget widget">
			<h4>Recommended Stories</h4>
			<div class="content"><?php echo $related_posts_html; ?></div>
		</div>

		<?php
	}
}
