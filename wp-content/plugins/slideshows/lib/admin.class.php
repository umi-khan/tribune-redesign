<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Slideshows_Admin
 *
 * @author amjad.sheikh
 */
class Slideshows_Admin
{   	
	public function hook_actions()
	{
		add_action( 'admin_init', array( $this, 'admin_init') );

		add_action( 'admin_print_scripts-edit.php'  , array( $this , 'enqueue_admin_posts_scripts' ) );
		add_action( 'admin_print_scripts-post-new.php'  , array( $this , 'enqueue_admin_posts_scripts' ) );
		add_action( 'admin_print_scripts-post.php'  , array( $this , 'enqueue_admin_posts_scripts' ) );
	}

	/**
	 * Add all your sections, fields and settings during admin_init
	 */
	function admin_init()
	{
		// Add the section to writing settings so we can add our fields to it
		add_settings_section('slideshow_settings_section', 'Slideshow Settings', array( $this, 'setting_section_callback' ), 'media');

		// Add the field with the names and function to use for our new settings, put it in our new section
		add_settings_field( 'slideshows-categories', 'Categories', array( $this, 'slideshow_settings' ), 'media', 'slideshow_settings_section' );

		// Register our setting so that $_POST handling is done for us and our callback function just has to echo the <input>
		register_setting( 'media', 'post_category' );
	}

	function setting_section_callback()
	{
		_e( 'For slideshow, at least one category should be selected from the following checked categories:' );
		_e('<br/><i>No category selection will disable category based restriction</i>');
	}

	function slideshow_settings ()
	{
		$selected_cats = get_option('post_category');
		echo '<div class="inner-sidebar" style="float:none;display:block;">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<div id="categorydiv" class="postbox">
							<div class="inside">
								<div id="taxonomy-category" class="categorydiv">
									<div id="category-all" class="tabs-panel">
										<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
									';
		wp_terms_checklist( 0 , array('selected_cats' => $selected_cats ));

		echo '					</div>
								</div>
							</div>
						</div>
					</div>
				</div>';
	}

	function enqueue_admin_posts_scripts()
	{
		global $pagenow, $post;

		$is_slideshow_page = ( ( ( $pagenow == "post-new.php" || $pagenow == "edit.php" ) && $_GET['post_type'] == SS_manager::TYPE_NAME ) || ( $pagenow == "post.php" && $post->post_type == SS_manager::TYPE_NAME ) );
		
		if( $is_slideshow_page )
		{
			$slideshows_cats = get_option('post_category');
			if( empty( $slideshows_cats ) ) return;

			array_unshift( $slideshows_cats, null );
			
			wp_enqueue_script( 'slideshow-category-admin', SLIDESHOWS_PLUGIN_URL . 'js/admin.js', array( 'jquery' ), '1.0', true );
			wp_localize_script( 'slideshow-category-admin', 'SlideshowsCats', $slideshows_cats );
		}
	}
}