<?php

/**
 * This class handles all the layout manager related functionality on the backend. It manages things like:
 * 1. displaying the metabox that lets the editors select proper layouts and categories for a post
 * 2. hook to actions and filters that manages the things to do when a post is deleted, or published, or updated
 * 3. includes the stylesheets and javascript files required on the admin side
 *
 * @package Layout_management
 */
class LM_admin
{
	// Number of layouts to be shown on add new post page
	const ADMIN_LAYOUTS_NUM	= 3;

	/**
	 * The purpose of this function is to hook to actions, and execute appropriate functions when actions occur.
	 * 
	 * This function hooks on to the following actions:
	 * 1. admin_menu - to display the layout manager meta box
	 * 2. publish_post - to do layout manager specific things when post is published
	 * 3. delete_post - to do layout manager specifi things when post is deleted
	 * 4. admin_print_scripts - to include javascript files needed by layout manager
	 * 5. admin_print_styles - to include stylesheets needed by layout manager
	 */
	public function hook_actions()
	{
		add_action( 'admin_menu', array( $this, 'manage_meta_boxes' ) );

		// post publishing related
		add_action( 'publish_post', array( $this, 'publish_post' ), 10, 2 );
		add_action( 'delete_post', array( $this, 'delete_post' ) );

		// insert all the required javascripts		
		add_action( 'admin_print_scripts-post.php', array( $this, 'admin_print_scripts' ) );
		add_action( 'admin_print_scripts-post-new.php', array( $this, 'admin_print_scripts' ) );

		// insert all the required css
		add_action( 'admin_print_styles-post.php', array( $this, 'init_stylesheets' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'init_stylesheets' ) );
	}

	/**
	 * The purpose of this function is to hook to filters and execute appropriate functions when actions occur.
	 *
	 * This function hooks on to the following filters:
	 * 1. category_save_pre - to modify the categories that the wordpress is going to save with the post
	 */
	public function hook_filters()
	{
		// category related
		add_filter( 'category_save_pre', array( $this, 'category_save_pre' ) );
	}

	public function admin_print_scripts()
	{		
      global $post;
      
      if( isset( $_GET['post_type'] ) ) $post_type = $_GET['post_type'];
      elseif( $post ) $post_type = $post->post_type;
      else $post_type = 'post';
      
		if( $post_type == 'post' ) wp_enqueue_script( 'lm-admin', LAYOUT_MANAGEMENT_PLUGIN_URL . 'js/admin.js', array( 'jquery' ), '1.3', true );
	}

	public function init_stylesheets()
   {	
      global $post;
      
      if( isset( $_GET['post_type'] ) ) $post_type = $_GET['post_type'];
      elseif( $post ) $post_type = $post->post_type;
      else $post_type = 'post';
            
      if( $post_type == 'post' ) wp_enqueue_style( 'lm_style' ,  LAYOUT_MANAGEMENT_PLUGIN_URL.'css/admin.css' );
   }

	public function manage_meta_boxes()
	{
		remove_meta_box( 'categorydiv', 'post', 'normal' );
		remove_meta_box( 'trackbacksdiv', 'post', 'normal' );
		remove_meta_box( 'slugdiv', 'post', 'normal' );
		remove_meta_box( 'authordiv', 'post', 'normal' );
		//remove_meta_box( 'tagsdiv-post_tag', 'post', 'normal' );
		remove_meta_box( 'postcustom', 'post', 'normal' );

		add_meta_box( 'lm-layout', 'Layout & Category Management', array( $this, 'display_layout_meta_box' ), 'post', 'side', 'high' );
	}

	/**
	 * This function is responsible for displaying the layout manager metaboxes, three metaboxes are shown, which allow
	 * the editor to include the post in at most 3 categories.
	 * This function relies on the LM_config class to get the name of the templates for each of the categories, the
	 * template names are like "Headline 1", "Sub heading 1", etc.
	 * The actual rendering of the metaboxes is done by the self::_display_layout_box() function.
	 */
	public function display_layout_meta_box()
	{
		$post_id     = $_GET['post'];
		$post_layout = LM_layout::get_layout( $post_id );
		
		$categories    = get_categories( array( 'orderby' => 'name', 'order' => 'ASC', 'hide_empty'=> false ) );

		// setup the templates
		$template_list = array();
		
		$template_list[LM_config::CATEGORY_HOME_ID] = LM_config::get_instance()->get_template_names( LM_config::CATEGORY_HOME_ID );

		foreach((array)$categories as $cat)
			$template_list[$cat->cat_ID] = LM_config::get_instance()->get_template_names( $cat->cat_ID );
		
		//fetching customized array of categories
		$custom_categories = $this->_parse_categories( $categories );
		
		$cat_parents = $custom_categories[0];
		unset( $custom_categories[0] );
		
		for( $i = 0; $i < self::ADMIN_LAYOUTS_NUM; $i++ )
			$this->_display_layout_box( $template_list, $cat_parents, $custom_categories, $post_layout[$i], $i );
	}

	/**
	 * This function is executed when a post is deleted, when the post is deleted, it first checks to see if the post
	 * being deleted is a revision or an actual post. If the post being deleted is a revision then nothing is done.
	 * Otherwise, the post is deleted from the layout manager table from all positions for all categories and this
	 * deletion is performed by LM_layout::remove()
	 *
	 * @param int $post_id The id of the post which is being deleted
	 * @return bool True on sucess, false otherwise
	 */
	public function delete_post($post_id)
	{
		$is_post_revision	= wp_is_post_revision( $post_id );
		
		$post_id	= ( false == $is_post_revision ) ? $post_id : $is_post_revision;

		if( empty( $post_id ) || false == is_numeric( $post_id ) ) return false;

		$post_layouts = LM_layout::get_layout( $post_id );
		foreach( (array)$post_layouts as $layout )
		{
			$lm_layout = new LM_layout( $layout['category_id'], $layout['group_id'], false );
			$lm_layout->remove( $layout['position'] );
		}

		return true;
	}

	/**
	 * The function is executed when the status of post is changed to publish, it first checks to see if the post
	 * being deleted is a revision or an actual post. If the post being deleted is a revision then nothing is done.
	 * Otherwise, the layout and category information of the post is saved in the layout manager table. One thing to note
	 * here is that, only a published post can have layout and category information attached to it. A post can be
	 * attached to a maximum of 3 categories and hence can have a maximum of 3 layouts.
	 *
	 * @param int $post_id The id of the post that is being published
	 * @param object $post The object of the post that is being published
	 */
	public function publish_post($post_id, $post)
	{
		$is_post_revision	= wp_is_post_revision( $post_id );

		$post_id	= ( false == $is_post_revision ) ? $post_id : $is_post_revision;

		if( empty( $post_id ) || false == is_numeric( $post_id ) ) return false;

		for( $i = 0; $i < self::ADMIN_LAYOUTS_NUM ; $i++ )
		{
			$current_category = $_POST['lm_category_' . $i . '_current'];
			$current_template = $_POST['lm_template_' . $i . '_current'];

			$new_category = $_POST['lm_category_' . $i . '_new'];
			$new_template = $_POST['lm_template_' . $i . '_new'];

			$this->_update_post_layout( $post_id, $current_category, $current_template, $new_category, $new_template );
		}
	}

	/**
	 * This function is fired before the categories selected for the post are saved in the posts table.
	 * We need this function because we have overriddent he wordpress functionality of categories. The layout manager has
	 * its own metabox that manages the categories and layouts of the posts, so that is where this function comes in. It
	 * populates the categories array with the categories selected from the layout manager metabox, so that wordpress can
	 * know of them and save them in the appropriate table.
	 *
	 * @param array $user_selected_categories The categories selected by the user for the post.
	 * @return array An array of categories, this return value must always be present otherwise categories attached to a
	 * post will be lost.
	 */
	public function category_save_pre($user_selected_categories)
	{
		$categories = array();

		for( $i = 0; $i < self::ADMIN_LAYOUTS_NUM ; $i++ )
		{
			$new_category = $_POST['lm_category_'.$i.'_new'];

			if( $new_category === '' || $new_category == 0 ) continue;

			$categories[] = $new_category;
		}

		//condition to make sure that user has not selected any categories and has selected a layout
		if( isset( $_POST['post_category'] ) ) $categories = array_merge( $categories, $user_selected_categories );

      return $categories;
	}

	/**
	 * This function renders a layout manager metabox. It creates the select boxes for categories and templates, and
	 * highlights the appropriate categories and templates if a post already has them.
	 *
	 * @param array $template_list
	 * @param array $main_categories An array of top level categories
	 * @param array $category_list An array of subcategories
	 * @param array $post_layout An array consisting of category_id and template_id which the post already has
	 * @param int $counter This is used to distinguish between the metaboxes, because there can be more than 1 metaboxes
	 * on the same page.
	 */
	private function _display_layout_box($template_list, $main_categories, $category_list, $post_layout, $counter)
	{
		$category_id = '';
		$template_id = '';
		
		// Check if this is already set in the layout
		if( $post_layout )
		{
			$category_id = $post_layout['category_id'];
			$template_id = $post_layout['template_id'];
		}
		
		$selected_template_names = $template_list['default'];
		if( $category_id !== '' && isset( $template_list[$category_id] ) ) $selected_template_names = $template_list[$category_id];
	?>
		
		<div id="lm_layout_metabox">
			Category: 
			<select name="lm_category_<?php echo $counter;?>_new" id="lm_category_<?php echo $counter;?>_new" class="lm_select lm_category_selector">
				<option value="" selected="selected">none</option>
				<option value="0" <?php selected( $category_id, '0' ); ?> >Home - Main</option>

				<?php foreach ($main_categories as $main_cat) : ?>
					<option value="<?php echo $main_cat->cat_ID; ?>" <?php selected( $category_id, $main_cat->cat_ID ); ?> >
						<?php echo ucwords( $main_cat->name ); ?> - Main
					</option>
				
				<?php
					$sub_category_list = $category_list[$main_cat->cat_ID];

					foreach( (array)$sub_category_list as $sub_cat ) :
				?>
					<option value="<?php echo $sub_cat->cat_ID; ?>" <?php selected( $category_id, $sub_cat->cat_ID ); ?> >
						<?php echo ucwords( $main_cat->name ) . ' - ' . ucwords( $sub_cat->name ); ?>
					</option>

				<?php
					endforeach;
				endforeach;
				?>
			</select>
			
			<br />Layout: &nbsp;&nbsp;&nbsp;
			<select name="lm_template_<?php echo $counter;?>_new" id="lm_template_<?php echo $counter;?>_new" class="lm_select">
				<option value="" selected="selected">none</option>
				<?php foreach( (array)$selected_template_names as $id => $name ) : ?>
					<option value="<?php echo $id; ?>" <?php selected( $template_id, $id ); ?> ><?php echo $name; ?></option>
				<?php endforeach; ?>
			</select>
			
			<input type="hidden" value="<?php echo $category_id;?>" name="lm_category_<?php echo $counter;?>_current"
					 id="lm_category_<?php echo $counter;?>_current" />
			<input type="hidden" value="<?php echo $template_id;?>" name="lm_template_<?php echo $counter;?>_current"
					 id="lm_template_<?php echo $counter;?>_current" />
			
			<?php 
				foreach( (array)$template_list as $category_id => $template ) :
					$select_options = '<option value="" selected="selected">none</option>';
			
					foreach( $template as $id => $name ) $select_options .= '<option value="' . $id . '">' . $name . '</option>';
			?>

				<input type="hidden" name="lm_hidden_template_<?php echo $category_id;?>" 
						 value="<?php echo htmlentities($select_options); ?>" id="lm_hidden_template_<?php echo $category_id; ?>" />
			<?php endforeach; ?>
			
		</div>
	<?php
	}

	/**
	 * This function is responsible for update the post's category and template information, any categories and templates
	 * that the post already have are deleted and the new categories and templates information is saved.
	 *
	 * @param int $post_id
	 * @param int $current_category
	 * @param int $current_template
	 * @param int $new_category
	 * @param int $new_template
	 */
	private function _update_post_layout($post_id, $current_category, $current_template, $new_category, $new_template)
	{
		if(!is_numeric($post_id))
			return false;
			
		if(($new_category ===  $current_category) && ($new_template === $current_template))
			return false;
			
		// remove the post from its current category
		if($current_category !== '' && $current_template !== '')
		{
			list($current_group, $current_positon) = explode('-', $current_template);		
			$layout = new LM_layout($current_category, $current_group, false);
			$layout->remove($current_positon);

			// invalidate the cache of the category page
			$this->_clear_cache( $current_category );
		}
		
		// insert the post in its new category
		if($new_category !== '' && $new_template !== '')
		{
			list($new_group, $new_positon) = explode('-', $new_template);
			$layout = new LM_layout($new_category, $new_group, false);
			$layout->insert($post_id, $new_positon);

			// invalidate the cache of the category page
			$this->_clear_cache( $new_category );
		}
	}
	
	private function _parse_categories($categories)
	{
		$result = array();		
		foreach( (array)$categories as $category_obj ) $result[$category_obj->parent][] = $category_obj;
		
		return $result;
	}

	/**
	 * This function purges the home url and the category url of the category_id that is passed to it.
	 * This function relies on the LM_cache class for the purging of cached pages.
	 * The reason why we need this function is because, although express cache can delete a post's category pages
	 * automatically, supercache cannot do so, and so this function is here just in case we need to switch over to
	 * supercache.
	 *
	 * @param int $category_id
	 * @return bool
	 */
	private function _clear_cache($category_id = 0)
	{
		$cache = LM_cache::get_cache_obj();

		if( false == ( $cache instanceof LM_cache ) ) return;

		$page_uri = ( $category_id == 0 ) ? get_home_url() : get_category_link( $category_id );

		// invalidate the current page
		$cache->invalidate_file( $page_uri );
	}
}