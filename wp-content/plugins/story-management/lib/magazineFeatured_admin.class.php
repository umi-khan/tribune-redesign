<?php

/**
 * Description of author_adminclass
 *
 * @author ovais.tariq
 */
class SM_magazineFeatured_admin
{
	const META_KEY = '_SM_isMagazineFeatured';
	
	public function hook_actions()
	{
		if( is_admin() )
		{
			// perform actions on admin initialization
			add_action( 'admin_init', array( $this, 'init_admin' ) );
		}
		add_action('save_post', array( $this, 'save_post' ) );
	}

	public function init_admin()
	{
		add_meta_box( 'sm_magazineFeatured_metabox', 'Sunday Magazine Featured',
							array( $this, 'display_metabox' ),
			  				'post', 'side');
	}

	public function display_metabox()
	{
		global $post_ID, $temp_ID;
		$post_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);

		$is_featured = ( get_post_meta($post_id, self::META_KEY, true) ) ? true : false;
		?>
		
		<input type="checkbox" <?php checked( $is_featured, true, true ); ?> 
				class="checkbox" id="<?php echo self::META_KEY?>" name="<?php echo self::META_KEY?>" />
		<label for="<?php echo self::META_KEY?>"> Magazine Featured Story</label>

		
		<?php
		
	}
	
	public function save_post( $post_id )
	{
		if ( !current_user_can( 'edit_post', $post_id ) || 'page' == $_POST['post_type'] ) return;
		$is_magazineFeatured = ( $_POST[self::META_KEY] ) ? (bool) $_POST[self::META_KEY] : false;
		update_post_meta($post_id,self::META_KEY,$is_magazineFeatured);
	}
}