<?php

/**
 * Description of author_adminclass
 *
 * @author Irfan.Ansari
 */
class SM_special_featured_admin
{
	const META_KEY = '_SM_isSpecialFeatured';
	const META_KEY_HIDE = '_isHideFeatured';
	const META_KEY_SPONSORED = '_et_sponsored_by';
	
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
		add_meta_box( 'sm_specialFeatured_metabox', 'Advanced Options',
							array( $this, 'display_metabox' ),
			  				'post', 'side');
	}

	public function display_metabox()
	{
		global $post_ID, $temp_ID;
		$post_id = (int) (0 == $post_ID ? $temp_ID : $post_ID);
		$is_specialfeatured = ( get_post_meta($post_id, self::META_KEY, true) ) ? true : false;
		$is_hidefeatured = ( get_post_meta($post_id, self::META_KEY_HIDE, true) ) ? true : false;
		
		if ( function_exists( 'run_sponsored_stories' ) ) {
			$et_sponsored_by = ( get_post_meta($post_id, self::META_KEY_SPONSORED, true) ) ? get_post_meta($post_id, self::META_KEY_SPONSORED, true) : 0 ;
		}
		
		?>
		<p><input type="checkbox" <?php checked( $is_specialfeatured, true, true ); ?> 
		class="checkbox" id="<?php echo self::META_KEY?>" name="<?php echo self::META_KEY?>" />
		<label for="<?php echo self::META_KEY?>"> Special Story (Without Side Bar)</label></p>
		<p><input type="checkbox" <?php checked( $is_hidefeatured, true, true ); ?> 
		class="checkbox" id="<?php echo self::META_KEY_HIDE?>" name="<?php echo self::META_KEY_HIDE?>" />
		<label for="<?php echo self::META_KEY_HIDE?>"> Hide Featured Picture</label></p>
		
		<?php if ( function_exists( 'run_sponsored_stories' ) ) {?>
        <p><label for="<?php echo self::META_KEY_SPONSORED?>">Sponsored by:</label>
		<select class="js-data-sponsored-ajax" name="<?php echo self::META_KEY_SPONSORED?>">
		  <?php if ( $et_sponsored_by ==0 ){ ?>
		  	<option value="0" selected="selected">Not Sponsored</option>
		  <?php }else{ ?>
		  	<option value="<?php echo $et_sponsored_by;?>" selected="selected"><?php echo get_the_title($et_sponsored_by);?></option>
		  	<option value="0">Not Sponsored</option>
		  <?php } ?>

		</select>
    	</p>
		<?php
		}	
	}
	
	public function save_post( $post_id )
	{
		if ( !current_user_can( 'edit_post', $post_id ) || 'page' == $_POST['post_type'] ) return;
		if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (defined('DOING_AJAX') && DOING_AJAX) || isset($_REQUEST['bulk_edit']))
        return;

		$is_specialFeatured = ( $_POST[self::META_KEY] ) ? (bool) $_POST[self::META_KEY] : false;
		$is_hidefeatured = ( $_POST[self::META_KEY_HIDE] ) ? (bool) $_POST[self::META_KEY_HIDE] : false;
		
		if ( function_exists( 'run_sponsored_stories' ) ) {
			$et_sponsored_by = ( $_POST[self::META_KEY_SPONSORED] ) ? $_POST[self::META_KEY_SPONSORED] : 0;
		}
		
		update_post_meta($post_id,self::META_KEY,$is_specialFeatured);
		update_post_meta($post_id,self::META_KEY_HIDE,$is_hidefeatured);
		
		if ( function_exists( 'run_sponsored_stories' ) ) {
			update_post_meta($post_id, self::META_KEY_SPONSORED, $et_sponsored_by);
		}
	}
}