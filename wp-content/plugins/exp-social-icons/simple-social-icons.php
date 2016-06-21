<?php
/*
Plugin Name: Social Icon Sidebar Widget
Plugin URI: http://express.pk
Description: A social icons widget for sidebar and story page.
Author: express
Author URI: http://www.express.pk
Version: 1.0
License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

class exp_social_icon extends WP_Widget {

	/**
	 * Default widget values.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Default widget values.
	 *
	 * @var array
	 */
	// protected sizes$;

	/**
	 * Default widget profile glyphs.
	 *
	 * @var array
	 */
	// protected $glyphs;

	/**
	 * Default widget profile values.
	 *
	 * @var array
	 */
	protected $profiles;

	/**
	 * Constructor method.
	 *
	 * Set some global values and create widget.
	 */
	function __construct() {

		/**
		 * Default widget option values.
		 */
		$this->defaults = apply_filters( 'simple_social_default_styles', array(
			'title'                  => 'Connect',
			'new_window'             => 0,
			'facebook_url'               => '',
			'twitter_url'                => ''
		) );
 

		$widget_ops = array(
			'classname'   => 'simple-social-icons',
			'description' => __( 'Displays select social icons.', 'ssiw' ),
		);

		$control_ops = array(
			'id_base' => 'simple-social-icons',
		);

		$this->WP_Widget( 'simple-social-icons', __( 'Social Icons Widget', 'ssiw' ), $widget_ops, $control_ops );

		/** Enqueue icon font */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ) );

		/** Load CSS in <head> */
		add_action( 'wp_head', array( $this, 'css' ) );

	}

	/**
	 * Widget Form.
	 *
	 * Outputs the widget form that allows users to control the output of the widget.
	 *
	 */
	function form( $instance ) {

		/** Merge with defaults */
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" /></p>
		<p><label><input id="<?php echo $this->get_field_id( 'new_window' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'new_window' ); ?>" value="1" <?php checked( 1, $instance['new_window'] ); ?>/> <?php esc_html_e( 'Open links in new window?', 'ssiw' ); ?></label></p>


		<hr style="background: #ccc; border: 0; height: 1px; margin: 20px 0;" />
		<p><label for="<?php echo $this->get_field_id( 'facebook_url' ); ?>"><?php _e( 'Facebook:', 'ssiw' ); ?></label> <input id="<?php echo $this->get_field_id( 'facebook_url' ); ?>" name="<?php echo $this->get_field_name( 'facebook_url' ); ?>" type="text" value="<?php echo esc_attr( $instance['facebook_url'] ); ?>" class="widefat"/></p>
		<p><label for="<?php echo $this->get_field_id( 'twitter_url' ); ?>"><?php _e( 'Twitter:', 'ssiw' ); ?></label> <input id="<?php echo $this->get_field_id( 'twitter_url' ); ?>" name="<?php echo $this->get_field_name( 'twitter_url' ); ?>" type="text" value="<?php echo esc_attr( $instance['twitter_url'] ); ?>" class="widefat" /></p>
		<hr style="background: #ccc; border: 0; height: 1px; margin: 20px 0;" />
		<p><label><input id="<?php echo $this->get_field_id( 'forsidebar' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'forsidebar' ); ?>" value="1" <?php checked( 1, $instance['forsidebar'] ); ?>/> <?php esc_html_e( 'Sidebar Widget?', 'ssiw' ); ?></label></p>

		<?php
	}

	/**
	 * Form validation and sanitization.
	 *
	 * Runs when you save the widget form. Allows you to validate or sanitize widget options before they are saved.
	 *
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		
		$instance['title'] = esc_attr($new_instance['title']);
		$instance['new_window'] = $new_instance['new_window'];
		$instance['facebook_url'] = $new_instance['facebook_url'];
		$instance['twitter_url'] = $new_instance['twitter_url'];
		$instance['forsidebar'] = $new_instance['forsidebar'];

		return $instance;
	}

	/**
	 * Widget Output.
	 *
	 * Outputs the actual widget on the front-end based on the widget options the user selected.
	 *
	 */
	function widget( $args, $instance ) {

		extract( $args );

		/** Merge with defaults */
		$instance = wp_parse_args( (array) $instance, $this->defaults );


		if ($instance['forsidebar']){
			//Widget frontend
			?>
 
			<div class="connect-widget connect-widget--tech">
			  <h4 class="connect-widget__title">
			    <span>
			      Connect With
			      <span class="logo">Tribune</span>
			      News
			    </span>
			  </h4>
			  <a class="connect-widget__button btn--facebook " <?php echo ($instance['new_window'])?"target=\"_blank\"":"";?> href="<?php echo (!empty($instance['facebook_url']))? $instance['facebook_url'] : "#";?>"><span class="fb"></span>Like Us On Facebook</a> 
			  <a class="connect-widget__button btn--twitter "  <?php echo ($instance['new_window'])?"target=\"_blank\"":"";?> href="<?php echo (!empty($instance['twitter_url']))? $instance['twitter_url'] : "#";?>"><span class="tw"></span>Follow Us On Twitter</a> 
			</div>
			  
		<?php }else{ 
			//story page frontend
			?>


			<div class="connect-widget connect-widget--tech">
			<div class="span-8 first no-bottom-border"> 
			<a class="connect-widget__button btn--facebook " <?php echo ($instance['new_window'])?"target=\"_blank\"":"";?> href="<?php echo (!empty($instance['facebook_url']))? $instance['facebook_url'] : "#";?>"><span class="fb"></span>Like Us On Facebook</a> 
			</div>

			<div class="span-8 last no-bottom-border"> 	
				<a class="connect-widget__button btn--twitter "  <?php echo ($instance['new_window'])?"target=\"_blank\"":"";?> href="<?php echo (!empty($instance['twitter_url']))? $instance['twitter_url'] : "#";?>"><span class="tw"></span>Follow Us On Twitter</a> 
			</div>
			</div>
 			<div class="clear"></div>
			  

 

		<?php }


	}

	function enqueue_css() {

		$cssfile	= apply_filters( 'simple_social_default_css', plugin_dir_url( __FILE__ ) . 'css/style.css' );

		wp_enqueue_style( 'simple-social-icons-font', esc_url( $cssfile ), array(), '1.0.5', 'all' );
	}

	/**
	 * Custom CSS.
	 *
	 * Outputs custom CSS to control the look of the icons.
	 */
	function css() {

		/** Pull widget settings, merge with defaults */
		$all_instances = $this->get_settings();
		$instance = wp_parse_args( $all_instances[$this->number], $this->defaults );

		$font_size = round( (int) $instance['size'] / 2 );
		$icon_padding = round ( (int) $font_size / 2 );

		/** The CSS to output */
		$css = '
		.simple-social-icons ul li a,
		.simple-social-icons ul li a:hover {
			background-color: ' . $instance['background_color'] . ' !important;
			border-radius: ' . $instance['border_radius'] . 'px;
			color: ' . $instance['icon_color'] . ' !important;
			font-size: ' . $font_size . 'px;
			padding: ' . $icon_padding . 'px;
		}

		.simple-social-icons ul li a:hover {
			background-color: ' . $instance['background_color_hover'] . ' !important;
			color: ' . $instance['icon_color_hover'] . ' !important;
		}';

		/** Minify a bit */
		$css = str_replace( "\t", '', $css );
		$css = str_replace( array( "\n", "\r" ), ' ', $css );

		/** Echo the CSS */
		echo '<style type="text/css" media="screen">' . $css . '</style>';

	}

}

add_action( 'widgets_init', 'ssiw_load_widget' );
/**
 * Widget Registration.
 *
 * Register Simple Social Icons widget.
 *
 */
function ssiw_load_widget() {

	register_widget( 'exp_social_icon' );

}