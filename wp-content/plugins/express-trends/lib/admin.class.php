<?php

/**
 * A class that manages admin related things related to tagging of stories
 */
class ET_admin
{
	public function init()
	{
		if( is_admin() ) $this->hook_actions();
	}

	public function hook_actions()
	{
		add_action( 'adminmenu', array( $this, 'manage_tag_metabox' ) );

		// insert all the required css
		add_action( 'admin_print_styles', array( $this, 'init_stylesheets' ) );
	}

	public function manage_tag_metabox()
	{
		global $wp_meta_boxes;

		if( is_array( $wp_meta_boxes ) && count( $wp_meta_boxes ) > 0 )
			array_walk_recursive( $wp_meta_boxes, array( $this, 'rename_tag_metabox' ) );
	}

	public function init_stylesheets()
   {
		?>

		<style type="text/css">
			#tagsdiv-post_tag .jaxtag .howto, #tagsdiv-post_tag #link-post_tag, #tagsdiv-post_tag .jaxtag .taghint {
				display: none;
			}
		</style>

		<?php
   }

	public function rename_tag_metabox(&$item, $key)
	{
		if( $key == 'title' && $item == 'Post Tags' ) $item = 'Trends';
	}
}