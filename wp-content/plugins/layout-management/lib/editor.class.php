<?php

/**
 * Description of editorclass
 *
 * @author ovais.tariq
 */
class LM_editor
{
	// Count of latest published stories to be displayed by the front end layout manager.
	const EDITOR_STORIES_PER_CATEGORY = 25;
	
   private $_categories;
	private $_subcategories;
	private $_stories;

   public function __construct()
   {
		$multimedia_category_id	= 33;
		$get_categories_args	   = array(
			'exclude'      => $multimedia_category_id,
			'child_of'     => 0
		);

		$home_cat = new stdClass();
		$home_cat->cat_ID = 0;
		$home_cat->parent = 0;
		$home_cat->slug   = 'home';
		$home_cat->name   = 'All';

		$this->_categories[] = $home_cat;
		$this->_stories[0] = $this->_get_stories_by_category( 0 );

		$categories = get_categories($get_categories_args);
		foreach( (array)$categories as $cat )
		{
			if( $cat->parent > 0 )
				$this->_subcategories[$cat->parent][] = $cat;
			else
				$this->_categories[] = $cat;

			$this->_stories[$cat->cat_ID] = $this->_get_stories_by_category( $cat->cat_ID );
		}
   }
   
   public function create_popup()
   {
		$categories    = $this->_categories;
		$subcategories = $this->_subcategories;
		$stories       = $this->_stories;

		$editor_template       = LAYOUT_MANAGEMENT_PLUGIN_DIR . 'templates/editor.php';
		$quickeditor_template  = LAYOUT_MANAGEMENT_PLUGIN_DIR . 'templates/quick-editor.php';
		$layouteditor_template = LAYOUT_MANAGEMENT_PLUGIN_DIR . 'templates/layout-editor.php';
		$storyeditor_template  = LAYOUT_MANAGEMENT_PLUGIN_DIR . 'templates/story-editor.php';
		$storysetter_template  = LAYOUT_MANAGEMENT_PLUGIN_DIR . 'templates/story-setter.php';
		$story_template        = LAYOUT_MANAGEMENT_PLUGIN_DIR . 'templates/story.php';

		include( $editor_template );
   }

	private function _get_stories_by_category($category_id)
	{
		global $wpdb;

		$category_id = (int)$category_id;

		$fields = "p.ID, p.post_title, p.post_excerpt";
		$joins  = "";
		$where  = "p.post_type = 'post' AND p.post_status = 'publish'";

		if( $category_id > 0 )
		{
			$joins  = " INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id";
			$joins .= " INNER JOIN {$wpdb->term_taxonomy} AS tt USING(term_taxonomy_id)";

			$where .= " AND tt.taxonomy = 'category' AND tt.term_id = $category_id";
		}

		$order_by = "ORDER BY p.post_date DESC";
		$limits   = "LIMIT " . self::EDITOR_STORIES_PER_CATEGORY;

		$sql = "SELECT $fields FROM {$wpdb->posts} AS p $joins WHERE $where $order_by $limits";

		return $wpdb->get_results( $sql );
	}
}