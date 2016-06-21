<?php

/**
 * This is the class that encapsulates all the configuration options. This class is implemented as a singleton.
 * Before going to any details about this class, there are some important terminology that should be understood,
 *
 * 1. Groups:
 * ==========
 * A page is divided into groups, group consists of stories, stories can have their positions shuffled or
 * can be cycled only within the group that they belong to. A page can define whether it contains a particular group or
 * not, but every page should have at least one group. Any page can have any number of stories in a group, for example
 * the home page can have 8 stories in Main Group, while Pakistan page only has 5 stories in Main Group, so that
 * really depends on the page.
 * There are three main groups:
 *		a. Main Stories Group: This is a group that has to be present in every page, this group consists of all the main
 *			stories such as headline and sub-headings.
 *		b. Featured Stories Group:  This is a group that is optional, and this group consists of featured stories.
 *		c. Special Story Group: This is a group that consists of a single story which is a breaking news story that is
 *			shown for any breaking news.
 * When adding a new group you have to do the following things, before you can start using it:
 *		a. Create a constant such as const GROUP_MAIN_STORIES = 0; with a unique integer value.
 *		b. Add the constant to the LM_config::$_groups array
 *
 * 2. Templates:
 * =============
 * A template is the design of a story, it actually defines the outlook of the story in the page. Examples
 * of templates are Headline1, Sub-heading3, More-story1, etc. A story can have a single template within a group, but
 * many stories within a group can have the same template, for example, we have a single story with the template
 * "Headline1" and four stories with the template "Sub-heading" on the home page.
 * There are five main templates:
 * 1. TEMPLATE_TOP_STORY: This is the template of the big headline on the page, and is called Headline1.
 * 2. TEMPLATE_SUB_STORY: This is the template for the sub-headings on the page, and is called Sub-headingN where N is a
 *		digit.
 * 3. TEMPLATE_MORE_STORIES: This is the template for the stories with only headings, and is called More-storiesN where
 *		N is a digit.
 * 4. TEMPLATE_FEATURED_STORIES: This is the template for the featured stories, and is called Featured-storiesN where
 *		N is a digit.
 * 5. TEMPLATE_SPECIAL_STORY: This is the template for the breaking news story that is shown on the home page, and is
 *		called Special-Story
 *		
 *
 * The main functionality provided by this class is:
 * 1. Groups and templates definition
 * 2. Category and sub-category pages template configuration
 *
 * @package Layout_management
 */
class LM_config
{
	// mysql table name
	const TABLE_NAME = "layout_management";

	// Excerpt Length in Characters.
	const STORY_EXCERPT_LENGTH	= 118;

	// the maximum number of stories which a category group can hold
	const MAX_STORIES_PER_CATEGORY_GROUP = 45;

	// category IDs
	const CATEGORY_DEFAULT_ID    = -1;
	const SUBCATEGORY_DEFAULT_ID = -2;
	const CATEGORY_HOME_ID       = 0;
	const CATEGORY_OPINION       = 268;
	const CATEGORY_SPORTS        = 16;
	const CATEGORY_WORLD        = 10;
	
	/**
	 * Groups are used for grouping stories, cycling of stories is local to the group to which that story belongs
	 */
	//	Group IDs for different groups of stories
	const GROUP_MAIN_STORIES     = 0;
	const GROUP_FEATURED_STORIES = 1;
	const GROUP_SPECIAL_STORY    = 2;
	const GROUP_TRENDING_STORY   = 3;

	// array of supported groups
	private $_groups = array( self::GROUP_MAIN_STORIES, self::GROUP_FEATURED_STORIES, self::GROUP_SPECIAL_STORY, self::GROUP_TRENDING_STORY );

	/**
	 * Template definitions, template definitions define how a story will look on a particular page
	 */
	// Template IDs for different story templates for the site will be defined here
	const TEMPLATE_TOP_STORY        = 0; // template id for top story
	const TEMPLATE_SUB_STORY        = 1; //	template id for sub story
	const TEMPLATE_MORE_STORIES     = 2; // template id for more stories
	const TEMPLATE_FEATURED_STORIES = 3; // template id for featured stories
	const TEMPLATE_SPECIAL_STORY    = 4; // template id for featured stories
	const TEMPLATE_TRENDING_STORIES = 5; // template id for Trending stories

	//	Template file names with respect to template ids
	private $_template_files = array(
		
		self::TEMPLATE_TOP_STORY			=>	'top-story',
		self::TEMPLATE_SUB_STORY			=>	'sub-story',
		self::TEMPLATE_MORE_STORIES		=>	'more-stories',
		self::TEMPLATE_FEATURED_STORIES	=>	'featured-stories',
		self::TEMPLATE_SPECIAL_STORY	   =>	'special-story',
		self::TEMPLATE_TRENDING_STORIES	   =>	'trend-story'
	);

	//	Template display names with respect to template ids
	private $_template_names = array(

		self::TEMPLATE_TOP_STORY        => 'Headline',
		self::TEMPLATE_SUB_STORY        => 'Sub Heading',
		self::TEMPLATE_MORE_STORIES     => 'More News',
		self::TEMPLATE_FEATURED_STORIES => 'Featured News',
		self::TEMPLATE_SPECIAL_STORY    => 'Special Story',
		self::TEMPLATE_TRENDING_STORIES    => 'Trending Story'
	);

	//	Template Directory name
	const TEMPLATES_DIR         = 'lm-templates';
	const WIDGETS_TEMPLATES_DIR = 'lm-templates/widgets';

	/**
	 * Template configuration for the category and sub-category pages.
	 * The category and subcategory pages have default template configuration defined here
	 * If you want to use the same configuration dont define the template values for the category, they will be inherited
	 * from the default configuration.
	 */
	private $_num_template_stories = array(

		/**
		 * This is the default template configuration for categories, if a category doesnt have configuration defined for
		 * it then it inherits these defaults.
		 * A category does not need to redefine the whole configuration array, it can just define the array keys that it
		 * wants to override.
		 */
		self::CATEGORY_DEFAULT_ID	=>	array(

			self::GROUP_MAIN_STORIES => array(
				self::TEMPLATE_TOP_STORY     => 1,
				self::TEMPLATE_SUB_STORY     => 5,
				self::TEMPLATE_MORE_STORIES  => 12
			),
			
			self::GROUP_FEATURED_STORIES => array(	self::TEMPLATE_FEATURED_STORIES => 4 )
			
		),

		/**
		 * This is the default template configuration for subcategories, if a subcategory doesnt have configuration
		 * defined for it then it inherits these defaults.
		 * A subcategory does not need to redefine the whole configuration array, it can just define the array keys that
		 * it wants to override.
		 */
		self::SUBCATEGORY_DEFAULT_ID	=>	array(

			self::GROUP_MAIN_STORIES => array(
				self::TEMPLATE_TOP_STORY    => 1,
				self::TEMPLATE_SUB_STORY    => 6,
				self::TEMPLATE_MORE_STORIES => 5
			),

			self::GROUP_FEATURED_STORIES => array(	self::TEMPLATE_FEATURED_STORIES => 6 )

		),

		/**
		 * The template configuration for home page category
		 */
		self::CATEGORY_HOME_ID	=>	array(

			self::GROUP_MAIN_STORIES => array(
				self::TEMPLATE_SUB_STORY     => 10,
				self::TEMPLATE_MORE_STORIES  => 6
			),

			self::GROUP_TRENDING_STORY => array( self::TEMPLATE_TRENDING_STORIES => 4 ),

			self::GROUP_FEATURED_STORIES => array(	self::TEMPLATE_FEATURED_STORIES => 10 ),

			self::GROUP_SPECIAL_STORY => array(	self::TEMPLATE_SPECIAL_STORY => 1 )

		),

		/**
		 * The template configuration for opinion category
		 */
		self::CATEGORY_OPINION	=>	array(

			self::GROUP_MAIN_STORIES => array(
				self::TEMPLATE_SUB_STORY     => 4,
				self::TEMPLATE_MORE_STORIES  => 8
			)

		),

		/**
		 * The template configuration for sports category
		 */
		self::CATEGORY_SPORTS	=>	array(

			self::GROUP_FEATURED_STORIES => array(	self::TEMPLATE_FEATURED_STORIES => 8 )

		),

		/**
		 * The template configuration for world category
		 */
		self::CATEGORY_WORLD	=>	array(

			self::GROUP_FEATURED_STORIES => array(	self::TEMPLATE_FEATURED_STORIES => 6 )

		)

	);

	//	static member which will hold the instance for this singleton class
	private static $_instance;

	private function __construct() {}

	/**
	 * @return LM_config Returns the instance of config class
	 */
	public static function get_instance()
	{
		if( false == ( self::$_instance instanceof self ) ) self::$_instance = new LM_config();
		
		return self::$_instance;
	}

	/**
	 * @param $category_id The category_id
	 * @param $group_id The group_id
	 * @return int Returns the number of stories in a group for a particular category
	 */
	public function get_num_group_stories($category_id, $group_id)
	{
		$num_tpl_stories = $this->get_num_template_stories( $category_id, $group_id );
		
		return array_sum( $num_tpl_stories );
	}
	
	/**
	 * The function below will return the number of stories to be shown for different templates on a page,
	 * @param	catetgory_id and group_id
	 * @return	array containing count of stories, with keys being template ids
	 */
	public function get_num_template_stories($cat_id, $group_id)
	{
		$cat_id    = (int)$cat_id;
		$group_id  = (int)$group_id;

		if( $cat_id > 0 )
		{
			$category       = get_category( $cat_id );
			$is_subcategory = ( $category->parent > 0 ) ? true : false;
		}

		$default_cat_id = ( $is_subcategory ) ? self::SUBCATEGORY_DEFAULT_ID : self::CATEGORY_DEFAULT_ID;
		$tpl_count      = $this->_num_template_stories[$default_cat_id][$group_id];

		foreach( (array)$this->_num_template_stories[$cat_id][$group_id] as $template_id => $count)
			$tpl_count[$template_id] = $count;
		
		return $tpl_count;
	}

	/**
	 * Generates an array of templates which is used on the admin section to let the editors select the layout of the
	 * story
	 * @param int $cat_id Category ID for which the template names array has to be generated
	 * @return array An array of template names
	 */
	public function get_template_names($cat_id = self::CATEGORY_DEFAULT_ID)
	{
		$tpl_names = array();
		
		foreach( (array)$this->_groups as $group_id )
		{
			$num_templates = 0;
			$tpl_count     = $this->get_num_template_stories( $cat_id, $group_id );

			foreach( (array)$tpl_count as $tpl_id => $count )
			{
				for($i = 0; $i < $count; $i++)
				{
					$tpl_names[$group_id . '-' . $num_templates] = $this->_template_names[$tpl_id] . ' - ' . ($i + 1);

					$num_templates++;
				}
			}
		}

		return $tpl_names;
	}
	
	/**
	 * Function to fetch the file name of template to be loaded from template folder along with the folder name
	 */
	public function get_template_path($template_id = 0)
	{
		if(!is_numeric($template_id) || !isset($this->_template_files[$template_id]))
			return false;

		$template_name = self::TEMPLATES_DIR . '/' . $this->_template_files[$template_id] . '.php';

		$template_path = (file_exists(TEMPLATEPATH . '/' . $template_name)) ?
										TEMPLATEPATH . '/' . $template_name :
										LAYOUT_MANAGEMENT_PLUGIN_DIR . $template_name;
		
		return $template_path;
	}
	
	/**
	 * @return array Returns the array of groups IDs
	 */
	public function get_groups()
	{
		return $this->_groups;
	}
}
