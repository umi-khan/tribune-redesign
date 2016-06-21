<?php

// RegisterMailchimp story Widget
function exp_mailchimp_story_widget() {
 
register_sidebar( array(
	'name' => 'Mailchimp Story Widget',
	'id' => 'mailchimp_story_widget',
	'before_widget' => '',
	'after_widget' => '',
	'before_title' => '',
	'after_title' => '',
) );
}
add_action( 'widgets_init', 'exp_mailchimp_story_widget' );
 
 
if (!function_exists('exp_display_story_links_widget'))
{
	function exp_display_story_links_widget($links, $show_comment_count = false)
	{
		if (is_array($links))
		{
			exp_load_widget_file("story-links", array("links"=>$links, "show_comment_count" => $show_comment_count));
		}	
	}
}


add_action("widgets_init", 'exp_page_column_widget_init');
function exp_page_column_widget_init()
{
	register_widget('PageColumnWidget');
}
class PageColumnWidget extends WP_Widget
{
	function  PageColumnWidget()
	{
		$this->WP_Widget(false, 'Featured Columns', array('description' => 'Shows featured columns list'));
	}

	function widget($args,$instance)
	{
		extract($args);
		exp_load_widget_file("column-stories");
	}
}


add_action("widgets_init", 'exp_tabbed_story_widget_init');
function exp_tabbed_story_widget_init()
{
	register_widget('TabbedStoryWidget');
}
class TabbedStoryWidget extends WP_Widget
{
	function  TabbedStoryWidget()
	{
		$this->WP_Widget(false, 'Tabbed Stories', array('description' => 'Display tabbed based story widget'));
	}

	// When Widget Control Form Is Posted
	function update($new_instance, $old_instance)
	{
		if (!isset($new_instance['submit'])) return false;
            
		$instance = $old_instance;
            
		$instance['mostviewed']    = $new_instance['mostviewed'] ? 1 : 0;
		$instance['mostcommented'] = $new_instance['mostcommented'] ? 1 : 0;
		$instance['mostemailed']   = $new_instance['mostemailed'] ? 1 : 0;
		$instance['mostliked']     = $new_instance['mostliked'] ? 1 : 0;
		$instance['limit']         = intval($new_instance['limit']);
		return $instance;
	}

	function form($instance)
	{
		global $wpdb;
		$instance = wp_parse_args( (array) $instance,
					array (
						'mostviewed'    => true,
						'mostcommented' =>true,
						'mostemailed'   =>true,
						'mostliked'     =>true,
						'limit'         => 10 )
					);
        
		$mostviewed    = isset( $instance['mostviewed'] ) ? (bool) $instance['mostviewed'] : false;
		$mostcommented = isset( $instance['mostcommented'] ) ? (bool) $instance['mostcommented'] : false;
		$mostemailed   = isset( $instance['mostemailed'] ) ? (bool) $instance['mostemailed'] : false;
		$mostliked     = isset( $instance['mostliked'] ) ? (bool) $instance['mostliked'] : false;
		$limit         = intval($instance['limit']);
       
		echo
			'<p>
				<input type="checkbox" name="'.$this->get_field_name('mostviewed').'" 
					id="'.$this->get_field_id('mostviewed').'" class="checkbox" '
					.checked( $mostviewed, true, false ).'/> 
				<label for="'.$this->get_field_id('mostviewed').'">Show most popular</label>
				<br/>
				
				<input type="checkbox" name="'.$this->get_field_name('mostcommented').'" 
					id="'.$this->get_field_id('mostcommented').'" class="checkbox" '
					.checked( $mostcommented, true, false ).'/> 
				<label for="'.$this->get_field_id('mostcommented').'">Show most commented</label>
				<br/>
				
				<input type="checkbox" name="'.$this->get_field_name('mostemailed').'" 
					id="'.$this->get_field_id('mostemailed').'" class="checkbox" '
					.checked( $mostemailed, true, false ).'/> 
				<label for="'.$this->get_field_id('mostemailed').'">Show most emailed</label>
				<br/>
				
				<input type="checkbox" name="'.$this->get_field_name('mostliked').'" 
					id="'.$this->get_field_id('mostliked').'" class="checkbox" '
					.checked( $mostliked, true, false ).'/> 
				<label for="'.$this->get_field_id('mostliked').'">Show most Liked</label>
				<br/>
				
				<label for="'.$this->get_field_id('limit').'">Number of stories per tab</label>
				<input id="'.$this->get_field_id('limit').'" 
					name="'.$this->get_field_name('limit').'" type="text" value="'.$limit.'" size="2"/>
				<input type="hidden" id="'.$this->get_field_id('submit').'" 
					name="'.$this->get_field_name('submit').'" value="1" />
			</p>';
    }

	function widget($args,$instance)
	{
		extract($args);
		$mostviewed    = isset( $instance['mostviewed'] ) ? (bool) $instance['mostviewed'] : false;
		$mostcommented = isset( $instance['mostcommented'] ) ? (bool) $instance['mostcommented'] : false;
		$mostemailed   = isset( $instance['mostemailed'] ) ? (bool) $instance['mostemailed'] : false;
		$mostliked     = isset( $instance['mostliked'] ) ? (bool) $instance['mostliked'] : false;
		
		$limit = intval($instance['limit']);
        
		$list = array();
		
		if($mostviewed) $list[]    =  array('type'=>'mostviewed', 'title'=>'Viewed');
		if($mostcommented) $list[] =  array('type'=>'mostcommented', 'title'=>'Commented');
		if($mostemailed) $list[]   =  array('type'=>'mostemailed', 'title'=>'E-mailed');
		if($mostliked) $list[]     =  array('type'=>'mostliked', 'title'=>'Liked');
		
		$data = array(
				"id" 	=> 1,
				"list" 	=> $list,
				"limit"	=> $limit
			);
		exp_load_widget_file("story-tabbed", $data);
	}
}

// One Stop Stories
if (!function_exists('exp_display_scrollable_categories_widget')) 
{
	/**
	 * This function will display a scrollable widget on main page containing Image or text with title of the top story of all categories
	 *
	 * @param
	 */
	function exp_display_scrollable_categories_widget()
	{
		$data = array();
		$data['categories_headlines'] = exp_get_categories_headlines();
		exp_load_widget_file("scrollable-categories",$data);
	}
}

if(!function_exists('_exp_get_stories_by_type'))
{
	function _exp_get_stories_by_type($type, $limit=5)
	{
		$posts = array();
		switch($type)
		{
			case 'mostviewed':
//				$posts = exp_most_popular_stories($limit);
                $options = array();
                $options['limit'] = 10;
                $options['range'] = 'daily';
                $posts = wmp_get_popular( $options );
				break;
		
			// Top Stories
			case 'top':
				$posts = array();
				break;
				
			// Most Commented Stories
			case 'mostcommented':
				$categoryid = exp_get_categoryid_by_uri();
				$posts = exp_most_commented_stories($limit, $categoryid);
				break;
				
			// Most Commented Stories
			case 'mostemailed':
				$posts = custom_mostemailed_posts($limit);
				break;
				
			// Most FB Liked Stories
			case 'mostliked':
				$posts = function_exists('SPP_get_popular_posts') ? SPP_get_popular_posts() : array();
				break;
				
				
			// if for a particular category
			default:
				$get_posts_args = array(
	    			'category' => $type, 'post_type' => 'post', 'orderby' => 'post_date', 'order' => 'DESC'
    			);
				$posts = get_posts($get_posts_args); 
				break;
		}
		
		if(count($posts) > $limit)
		{
			$posts = array_slice($posts, 0, $limit);
		}
		return $posts;
	}
}

add_action("widgets_init", 'exp_advertisement_widget_init');
function exp_advertisement_widget_init()
{
	register_widget('Advertisement');
}
class Advertisement extends WP_Widget
{
	private $adtypes = array(
		'box_ad'        => 'Right Box Ad',
      'box_ad_bottom' => 'Right Box Ad Bottom',
		'halfbanner_ad' => 'Right Half Banner'	
	);
	
	function  Advertisement()
	{
		$this->WP_Widget(false, 'Advertisement', array('description' => 'Displays advertisement widget'));
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['ad_type'] = $new_instance['ad_type'];
		return $instance;
	}

	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'ad_type' => $this->adtypes['box_ad'] ) );
		$selected_ad_type = $instance['ad_type'];
		
		?>
			<label for="<?php echo $this->get_field_id( 'ad_type' );?>">Ad Type:</label>
			<select id="<?php echo $this->get_field_id( 'ad_type' );?>" name="<?php echo $this->get_field_name( 'ad_type' );?>">
				<?php foreach ($this->adtypes as $key => $ad_type) :?>
				<option value="<?php echo $key;?>" <?php selected( $key, $selected_ad_type ); ?>>
					<?php echo $ad_type;?>
				</option>
				<?php endforeach;?>
			</select>
		<?php
	}
	
	function widget($args,$instance)
	{
		extract($args);
		$ad_type = $instance['ad_type'];
		if( $ad_type == 'box_ad' ) exp_load_widget_file("advertisement");
		else if ($ad_type == 'halfbanner_ad' ) exp_load_widget_file("advertisement-halfbanner");		
      else if( $ad_type == 'box_ad_bottom' ) exp_load_widget_file("advertisement-bottom");
	}
}

function exp_display_advertisement_widget()
{
    exp_load_widget_file("advertisement");
}

add_action("widgets_init", 'exp_facebook_recommendation_widget_init');
function exp_facebook_recommendation_widget_init()
{
	register_widget('FBRecommendationWidget');
}
class FBRecommendationWidget extends WP_Widget
{
	function  FBRecommendationWidget()
	{
		$this->WP_Widget(false, 'Facebook Recommendations', array('description' => 'Display personalized recommendations'));
	}

	// When Widget Control Form Is Posted
	function update($new_instance, $old_instance)
	{
		if (!isset($new_instance['fb_recommendation_submit'])) return false;
            
		$instance = $old_instance;
		$instance['header'] = $new_instance['header'] ? 1 : 0;
		$instance['width'] = intval($new_instance['width']);
		$instance['height'] = intval($new_instance['height']);
		return $instance;
	}

	function form($instance)
	{
		global $wpdb;
		$instance = wp_parse_args( (array) $instance,
					array(
						'header' => true,
						'width'=>300,
						'height' => 300)
					);

		$header = isset( $instance['header'] ) ? (bool) $instance['header'] : false;
		$width = intval($instance['width']);
		$height = intval($instance['height']);

		echo
			'<p>
				<input type="checkbox" name="'.$this->get_field_name('header').'" id="'.$this->get_field_id('header').'" class="checkbox" '.checked( $header, true, false ).'/> <label for="'.$this->get_field_id('header').'">Show header</label>
				<br/>
				<label for="'.$this->get_field_id('width').'">Width</label><input id="'.$this->get_field_id('width').'" name="'.$this->get_field_name('width').'" type="text" value="'.$width.'" size="3"/>
				<br/>
				<label for="'.$this->get_field_id('height').'">Height</label><input id="'.$this->get_field_id('height').'" name="'.$this->get_field_name('height').'" type="text" value="'.$height.'" size="3"/>
				<br/>
				<input type="hidden" id="'.$this->get_field_id('fb_recommendation_submit').'" name="'.$this->get_field_name('fb_recommendation_submit').'" value="1" />
			</p>';
	}

	function widget($args,$instance)
	{
		extract($args);
		
		$header = (isset( $instance['header']) &&  $instance['header'] == 1 ) ? 'true' : 'false';
		$width = intval($instance['width']);
		$height = intval($instance['height']);
		$siteurl = home_url();
		exp_insert_script('fb-sdk');
		  
		printf('<div style="height:%spx" class="widget"><fb:recommendations site="%s" width="%spx" height="%spx" header="%s"></fb:recommendations></div>', $height, $siteurl, $width, $height, $header);
	}
}

/*
 * The code below adds a simple widget for the links of twitter and rss feed of the site.
 */

add_action("widgets_init", array('EXP_social_links','init'));
class EXP_social_links extends WP_Widget
{
	private static $WIDGET_TITLE = 'Connect';
	function  EXP_social_links()
	{
		$this->WP_Widget('EXP_social_links', 'Social Links', array('description' => 'Displays the links for twitter, Facebook page and RSS feed for the site'));
	}
    
	public static function init()
	{
		register_widget('EXP_social_links');
	}

	//When Widget Control Form Is Posted
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['twitter']    = $new_instance['twitter'] ? 1 : 0;
		$instance['rss_feed']   = $new_instance['rss_feed'] ? 1 : 0;
		$instance['facebook']   = $new_instance['facebook'] ? 1 : 0;
		$instance['newsletter'] = $new_instance['newsletter'] ? 1 : 0;
		$new_instance['title']  = trim($new_instance['title']);
		
		if($new_instance['title']) $instance['title'] = $new_instance['title'];
		
		return $instance;
	}
	function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array(
				'twitter'    => true,
				'rss_feed'   => true,
				'facebook'   => true,
				'newsletter' => false,
				'title'      => self::$WIDGET_TITLE
			 )
		);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title</label> <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" id="<?php echo $this->get_field_id('title'); ?>" class="text" />
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name('facebook'); ?>" value="1" id="<?php echo $this->get_field_id('facebook'); ?>" class="checkbox" <?php echo checked( $instance['facebook'], true, false ) ?> /> <label for="<?php echo $this->get_field_id('facebook'); ?>">Show link to Facebook</label>
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name('twitter'); ?>" value="1" id="<?php echo $this->get_field_id('twitter'); ?>" class="checkbox" <?php echo checked( $instance['twitter'], true, false ) ?> /> <label for="<?php echo $this->get_field_id('twitter'); ?>">Show link to Twitter</label>
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name('rss_feed'); ?>" value="1" id="<?php echo $this->get_field_id('rss_feed'); ?>" class="checkbox" <?php echo checked( $instance['rss_feed'], true, false ) ?> /> <label for="<?php echo $this->get_field_id('rss_feed'); ?>">Show link to RSS feed</label>
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name('newsletter'); ?>" value="1" id="<?php echo $this->get_field_id('newsletter'); ?>" class="checkbox" <?php echo checked( $instance['newsletter'], true, false ) ?> /> <label for="<?php echo $this->get_field_id('newsletter'); ?>">Show link for Newsletter</label>
		</p>
<?php 
    }

	function widget($args,$instance)
	{
		extract($args);
		
		$twitter    = (isset( $instance['twitter']) &&  $instance['twitter'] == 1 ) ? true : false;
		$rss_feed   = (isset( $instance['rss_feed']) &&  $instance['rss_feed'] == 1 ) ? true : false;
		$facebook   = (isset( $instance['facebook']) &&  $instance['facebook'] == 1 ) ? true : false;
		$newsletter	= (isset( $instance['newsletter']) &&  $instance['newsletter'] == 1 ) ? true : false;

		$title = ($instance['title']) ? $instance['title'] : self::$WIDGET_TITLE;
		
		if(!$twitter && !$rss_feed && !$facebook ) return false;
		
		$template_url = get_bloginfo('template_url', 'display');
		$site_name    = esc_html( get_bloginfo('name') );
		$home_url     = home_url();
			
		echo '<div id="connect" class="widget">';
		echo '<h4 class="title">'.$title.'</h4>';
			echo '<div class="content clearfix">';
				echo '<ul class="links">';
				  
					if($facebook)
					{
						echo '<li><a href="http://www.facebook.com/etribune" target="_blank" title="Connect with '.$site_name.' on Facebook" ><img src="'.$template_url.'/img/facebook-icon.gif" alt="" width="33" height="33" /></a></li>';
					}
					if($twitter)
					{
						echo '<li><a href="http://www.twitter.com/etribune" target="_blank" title="Follow '.$site_name.' on Twitter" ><img src="'.$template_url.'/img/twitter-icon.gif" alt="" width="33" height="33" /></a></li>';
					}
					if($rss_feed)
					{
						echo '<li><a href="'.$home_url.'/feed/rss" target="_blank" title="Subscribe to RSS feed" ><img src="'.$template_url.'/img/rss-icon.gif" alt="" width="33" height="33" /></a></li>';
					}
					if($newsletter)
					{
						echo '<li><a id="newsletterpopup" name="newsletterpopup" href="'.$home_url.'/newsletter" title="Signup for Newsletter" ><img src="'.$template_url.'/img/email.png" alt="" width="21" height="21" /></a></li>';
					}
					
				echo '</ul>';
			echo '</div>';
		echo '</div>';
	}
}


/*
 * The code below adds the link to the eMagazine
 */

add_action("widgets_init", array('Tribune_magazine','init'));
class Tribune_magazine extends WP_Widget
{
	const WIDGET_TITLE = 'Tribune Magazine';
	
	function __construct()
	{
		parent::__construct('Tribune_magazine', 'Tribune Magazine Widget', array('description' => 'Displays the link to the eMagazine'));
	}
    
	public static function init()
	{
		register_widget('Tribune_magazine');
	}

	function widget($args,$instance)
	{
		$last_sunday_stamp = '';
		$last_sunday_stamp = (strtolower(date('l')) != 'sunday') ? strtotime('last Sunday') : strtotime("now");
		
		?>
		<div class="clearfix">
			<div class="col-lg-6">
				<div id="epaper-widget" class="widget button">
					<a href="/epaper/">
						<img alt="" src="<?php echo get_template_directory_uri() ?>/img/epaper/top-default.gif" width="99" height="30">
						<img class="cover-thumbnail" alt="View ePaper" src="<?php echo get_template_directory_uri() ?>/img/epaper/cover.gif" width="125" height="156" />
					</a>
				</div>
			</div>
			<div class="col-lg-6 last">
				<div id="magazine-widget" class="widget button">
					<a href="/emagazine/">
						<img alt="" src="<?php echo get_template_directory_uri() ?>/img/mag/top-default.gif" width="102" height="30">
						<img class="cover-thumbnail" alt="View Magazine" src="<?php echo get_template_directory_uri() ?>/img/mag/cover.jpg?v=<?php echo $last_sunday_stamp;?>" width="125" height="156" />
					</a>
				</div>
			</div>
		</div>
		<div class="clearfix">
            <div class="span-8 last">
				<div id="mst" class="widget button">
					<a href="/ms-t/">
						<img class="cover-thumbnail" alt="View ePaper" src="<?php echo get_template_directory_uri() ?>/img/ms-t/cover.png" width="78" height="66"/>
					</a>
				</div>
			</div>
            <div class="span-8 last">
				<div id="hifive" class="widget button">
		<a href="/hi-five/">
		<img class="cover-thumbnail" alt="View Magazine" src="<?php echo get_template_directory_uri() ?>/img/hi-five/cover.png?v=<?php echo $last_sunday_stamp;?>" width="78" height="66"/>
					</a>
				</div>
			</div> 

		</div>
		
		<?php 
	}
}

/*
 * The code below adds the link to the ePaper
 */

add_action("widgets_init", array('Tribune_ePaper','init'));
class Tribune_ePaper extends WP_Widget
{
	function __construct()
	{
		parent::__construct('Tribune_ePaper', 'Tribune ePaper Link Widget', array('description' => 'Displays the link to the ePaper'));
	}
    
	public static function init()
	{
		register_widget('Tribune_ePaper');
	}
	
	function widget($args,$instance)
	{
		?>
		<div class="widget button clearfix">
			<a href="<?php echo esc_url( home_url() ) ?>/epaper/">
				<img class="left" alt="ePaper" src="<?php echo get_template_directory_uri() ?>/img/epaper/banner.gif?v=0.1" width="303px" height="66px" />
			</a>
		</div>
		<?php 
	}
}

/*
 * The code below adds the link to the facebook Page
 */

add_action("widgets_init", array('Tribune_fb_page','init'));
class Tribune_fb_page extends WP_Widget
{
	function __construct()
	{
		parent::__construct('Tribune_fb_page', 'Tribune FB-Page Link Widget', array('description' => 'Displays the link to the Facebook Page'));
	}
    
	public static function init()
	{
		register_widget('Tribune_fb_page');
	}
	
	function widget($args,$instance)
	{
		?>
		<div id="fb-banner" class="widget button clearfix">
			<a href="http://facebook.com/etribune/" title="Join <?php echo esc_html(get_bloginfo('name'));?> on Facebook">
				<img alt="" src="<?php echo get_template_directory_uri() ?>/img/fb-banner.jpg" width="305px" height="66px" />
			</a>
		</div>
		<?php 
	}
}

function the_weather()
{
	if( class_exists('Plugin_weather') )
	{
		return Plugin_weather::display_widget('weather/');
	}
}


/*
 * The code below adds the mosts popular stories with images and excerpts
 */

add_action("widgets_init", array('Most_popular_stories','init'));
class Most_popular_stories extends WP_Widget
{
	function __construct()
	{
		parent::__construct('Most_popular_stories', 'Most Popular Stories with Excerpts', array('description' => 'Displays most popular stories with excerpts and images'));
	}
    
	public static function init()
	{
		register_widget('Most_popular_stories');
	}
	
	// When Widget Control Form Is Posted
	function update($new_instance, $old_instance)
	{
    	$instance = $old_instance;
		$instance['limit'] = intval($new_instance['limit']);
		return $instance;
	}

	function form($instance)
	{
		global $wpdb;
		$instance = wp_parse_args( (array) $instance, array( 'limit' => 5 ) );
      $limit = intval($instance['limit']);
       
		echo
			'<p>
				<label for="'.$this->get_field_id('limit').'">Number of stories</label><input id="'.$this->get_field_id('limit').'" name="'.$this->get_field_name('limit').'" type="text" value="'.$limit.'" size="2"/>
			</p>';
	}
	
	function widget($args,$instance)
	{
		$limit = intval($instance['limit']);
		exp_load_widget_file("popular-stories", array("stories"=>exp_most_popular_stories($limit) ));
	}
}


// Trending Story Widget
add_action("widgets_init", array('TrendingNewsWidget','init') );
class TrendingNewsWidget extends WP_Widget
{
	public function __construct()
	{
		$options = array( 'description' => 'Display Trending News widgets.' );

		parent::__construct( 'TrendingNewsWidget', 'Trending Stories', $options );
	}
	
	public static function init()
	{
		register_widget( 'TrendingNewsWidget' );
	}

	public function update($new_instance, $old_instance)
	{
		$instance             = $old_instance;
		return $instance;
	}

	public function form($instance)
	{
	 
 
    }

   	function widget($args, $instance)
	{
      ?>
				<?php exp_load_widget_file( 'trending-news'); ?>
	
<?php
	}


	// function widget($args, $instance)
	// {



	// }
}

// Opinion Widget
add_action("widgets_init", array('OpinionWidget','init') );
class OpinionWidget extends WP_Widget
{
    public function __construct()
    {
        $options = array( 'description' => 'Display Opinion widgets.' );

        parent::__construct( 'OpinionWidget', 'Opinion', $options );
    }
    
    public static function init()
    {
        register_widget( 'OpinionWidget' );
    }

    public function update($new_instance, $old_instance)
    {
        $instance             = $old_instance;
        return $instance;
    }

    public function form($instance)
    {
     

   }

      function widget($args, $instance)
    {
     ?>
        <div class="clearfix">
            <div class="span-8">
                <?php exp_load_widget_file( 'opinion'); ?>
            </div>
          </div>        
<?php
    }


    // function widget($args, $instance)
    // {



    // }
}


// More news widget starts
add_action("widgets_init", array('MoreNewsWidget','init') );
class MoreNewsWidget extends WP_Widget
{
	public function __construct()
	{
		$options = array( 'description' => 'Display two widgets having More News and category More News.' );

		parent::__construct( 'MoreNewsWidget', 'More News', $options );
	}
	
	public static function init()
	{
		register_widget( 'MoreNewsWidget' );
	}

	public function update($new_instance, $old_instance)
	{
		$instance             = $old_instance;
		$instance['category'] = $new_instance['category'];

		return $instance;
	}

	public function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'category' => 0 ) );
		$category = $instance['category'];

		$categories = get_categories( array( 'parent' => 0, 'orderby' => 'id' ) );

		?>
		Show Posts from category:
		<p>
			<select name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>">
            <option value="0" <?php selected( '0', $category ); ?>>All</option>
				<option value="-1" <?php selected('-1', $category ); ?>>Category's Default</option>
				<?php foreach( $categories as $cat ) : ?>
				<option value="<?php echo $cat->cat_ID; ?>" <?php selected( $cat->cat_ID, $category ); ?>>
					<?php echo $cat->cat_name; ?>
				</option>
				<?php endforeach;?>
			</select>
		</p>
		<?php
    }

    private function _get_current_category_id()
	{
		global $wp_query;
		$category = $wp_query->get_queried_object();
		return $category->cat_ID ;

	}
   
	function widget($args, $instance)
	{
		extract($args);

	  $category = ( $instance['category'] == -1 ) ? $this->_get_current_category_id() : $instance['category'];      
      
      $is_middle_sidebar = (bool)strpos(strtolower($args['name']),'middle');            
      
      $data = array( 'category_id' => $category, 'is_middle_sidebar' => $is_middle_sidebar );
      ?>
		<div class="clearfix">
			<div<?php if(false === $is_middle_sidebar ):?> class="span-4"<?php endif;?>>
				<?php exp_load_widget_file( 'morenews' , $data ); ?>
			</div>
         <?php if(false === $is_middle_sidebar ):?>
			<div class="span-4 last">
				<?php exp_load_widget_file( 'morenews-categories', array( 'category_id' => $category ) ); ?>
			</div> 
         <?php endif;?>
		</div>		
<?php
	}
}

add_action("widgets_init", array('FeaturedStoriesWidget','init') );
class FeaturedStoriesWidget extends WP_Widget
{
	public function __construct()
	{
		$options = array( 'description' => 'Display two widgets having featured stories and category featured stories.' );

		parent::__construct( 'FeaturedStoriesWidget', 'Featured Stories', $options );
	}
	
	public static function init()
	{
		register_widget( 'FeaturedStoriesWidget' );
	}

	public function update($new_instance, $old_instance)
	{
		$instance             = $old_instance;
		$instance['category'] = $new_instance['category'];

		return $instance;
	}

	public function form($instance)
	{
		$instance = wp_parse_args( (array) $instance, array( 'category' => 0 ) );
		$category = $instance['category'];

		$categories = get_categories( array( 'parent' => 0, 'orderby' => 'id' ) );

		?>
		Show Posts from category:
		<p>
			<select name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>">
            <option value="0" <?php selected( '0', $category ); ?>>All</option>
				<option value="-1" <?php selected('-1', $category ); ?>>Category's Default</option>
				<?php foreach( $categories as $cat ) : ?>
				<option value="<?php echo $cat->cat_ID; ?>" <?php selected( $cat->cat_ID, $category ); ?>>
					<?php echo $cat->cat_name; ?>
				</option>
				<?php endforeach;?>
			</select>
		</p>
		<?php
    }

    private function _get_current_category_id()
	{
		global $wp_query;
		$category = $wp_query->get_queried_object();
		return $category->cat_ID ;

	}
   
	function widget($args, $instance)
	{
		extract($args);

		$category = ( $instance['category'] == -1 ) ? $this->_get_current_category_id() : $instance['category'];      
      
      $is_middle_sidebar = (bool)strpos(strtolower($args['name']),'middle');            
      
      $data = array( 'category_id' => $category, 'is_middle_sidebar' => $is_middle_sidebar );
      ?>
		<div class="clearfix">
			<div<?php if(false === $is_middle_sidebar ):?> class="span-4"<?php endif;?>>
				<?php exp_load_widget_file( 'featured' , $data ); ?>
			</div>
         <?php if(false === $is_middle_sidebar ):?>
			<div class="span-4 last">
				<?php exp_load_widget_file( 'featured-categories', array( 'category_id' => $category ) ); ?>
			</div> 
         <?php endif;?>
		</div>		
<?php
	}
}

add_action( "widgets_init", array('LettersWidget','init') );
class LettersWidget extends WP_Widget
{
	public static function init()
	{
		register_widget('LettersWidget');
	}
	
	function LettersWidget()
	{
		$this->WP_Widget(false, 'Letters Widget', array('description' => 'Display Letters'));
	}

	function widget($args,$instance)
	{
		extract($args);
		
		$letters_cat      = get_category_by_slug( 'letters' );
		$letters_layout   = new LM_layout( $letters_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 6 );
		$letters_stories  = $letters_layout->posts_stack;
		$letters_cat_link = get_category_link( $letters_cat->cat_ID );
		
		if( empty( $letters_stories ) ) return;
		
		?>
		
		<div class="letters-widget widget">
			<h4>
				<a href="<?php echo $letters_cat_link;?>"> <?php echo $letters_cat->name; ?> </a>
			</h4>
			
			<div class="carousel-pagination"></div>
			
			<div class="content">
				<div class="carousel clear">
					<div class="items clearfix">
						<?php
							$have_posts = true;
							$num_posts_per_page = 2;
							$num_pages = ceil ( count( $letters_stories ) / $num_posts_per_page );

							for($i = 0; $i < $num_pages; $i++ ) :
								if( !$have_posts ) break;
								
								$current_class = '';
								if( $i == 0) $current_class = ' first';
								if( $i == $num_pages-1 ) $current_class = ' last';
						?>
							<div class="item<?php echo $current_class; ?>">
								<?php
									for($j = 0; $j < $num_posts_per_page; $j++ ) :
										$story = array_shift($letters_stories);
										if( !$story || empty( $story ) )
										{
											$have_posts = false;
											break;
										}
									?>

									<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?>">
										<h2>
											<a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a>
										</h2>
										<p class="excerpt"><?php echo $story->excerpt;?></p>
										<span class="timestamp" title="<?php echo $story->date_gmt;?>"></span>
									</div>
								<?php endfor; ?>
							</div>
						<?php endfor; ?>
					</div>
				</div>
						
				<div class="letters-info">
					<h4>Submit a letter</h4>
					<img src="<?php echo get_template_directory_uri() ?>/img/letter-icon.gif" alt="" width="44" height="54" />
					<p>
						Letters will be edited for policy, content and clarity. All letters must have the writer's
						name and address. You may send your letters to:<br/>
						<a href="mailto:letters@tribune.com.pk">letters@tribune.com.pk</a>
					</p>
				</div>
			</div>
		</div>		
		<?php		
	}
}

add_action( 'widgets_init', array( 'Tribune_Recent_Comments_Widget', 'init' ) );
class Tribune_Recent_Comments_Widget extends WP_Widget_Recent_Comments
{
	public static function init()
	{
		register_widget( __CLASS__ );
	}
	
	function widget( $args, $instance )
	{
		global $comments, $comment;

		$cache = wp_cache_get('widget_recent_comments', 'widget');

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		extract($args, EXTR_SKIP);
		$output = '';
		$title = apply_filters('widget_title', empty($instance['title']) ? 'Recent Comments' : $instance['title']);

		if ( ! $number = (int) $instance['number'] )
			$number = 5;
		else if ( $number < 1 )
			$number = 1;

		$comments = get_comments( array( 'number' => $number, 'status' => 'approve' ) );
		$output .= $before_widget;
		
		if ( $title ) $output .= $before_title . $title . $after_title;
		
		if ( $comments )
		{
			$comments_count = count( $comments );
			$counter = 0;
			foreach ( (array) $comments as $comment)
			{				

				$class   = ( $counter++ == 0 ) ? 'first' : (  $counter == $comments_count ? 'last' : '' );

				$output .=  sprintf( _x('
				<div class="comment %s">
					<h2>
						<a class="title" href="%s">%s</a>
					</h2>
					<span class="author">%s</span>
					<p class="excerpt">%s</p>
				</div>' , 'widgets' ),
						  $class, esc_url( get_comment_link($comment->comment_ID) ),
						  get_the_title( $comment->comment_post_ID ) , get_comment_author_link(), get_comment_excerpt($comment->comment_ID)
						  );
			}
		}

		$output .= $after_widget;

		echo $output;
		$cache[$args['widget_id']] = $output;
		wp_cache_set('widget_recent_comments', $cache, 'widget');
	}
}

add_action( "widgets_init", array('NetworkServicesWidget','init') );
class NetworkServicesWidget extends WP_Widget
{
	public static function init()
	{
		register_widget('NetworkServicesWidget');
	}
	
	function NetworkServicesWidget()
	{
		$this->WP_Widget(false, 'Network & Services Links', array('description' => 'Display Social Networks and Tribune Services Links'));
	}
	
	function widget($args,$instance)
	{
		extract($args);
		$template_url = trailingslashit( get_template_directory_uri() );
		?>
		
		<div class="network-services-widget widget">
			<h4>Follow Us</h4>
			
			<div class="content clearfix">
				<ul class="first">
					<li>
						<a target="_blank" href="http://www.facebook.com/etribune/">
							<img src="<?php echo $template_url; ?>img/social-icons/facebook.jpg" width="20" height="20" alt="" />
							Facebook
						</a>
					</li>
				</ul>
			
				<ul class="last">
					<li>
						<a target="_blank" href="http://www.twitter.com/etribune/">
							<img src="<?php echo $template_url; ?>img/social-icons/twitter.jpg" width="20" height="20" alt="" />
							Twitter
						</a>
					</li>
				</ul>
			</div>
		</div>
		
		<?php
	}
}

add_action( "widgets_init", array('Tribune_Recent_Posts','init') );
class Tribune_Recent_Posts extends WP_Widget_Recent_Posts
{
	const DEFAULT_CATEGORY = 0;
	
	public static function init()
	{
		register_widget( __CLASS__ );
		add_filter( 'widget_update_callback', array( __CLASS__ , 'widget_update_callback') , 10, 4 );
		add_filter( 'widget_form_callback', array( __CLASS__ , 'widget_form_callback') , 10, 2 );
		add_filter( 'in_widget_form', array( __CLASS__ , 'in_widget_form'), 10, 3 );
	}
	
	function widget_update_callback(  $instance, $new_instance, $old_instance, $widget )
	{		
		if( get_class( $widget ) == get_parent_class() )	$instance['category'] = $new_instance['category'];

		return $instance;
	}

	public function widget_form_callback( $instance, $widget )
	{
		if( get_class( $widget ) == __CLASS__ )				
			$instance['category'] = isset($instance['category']) ? intval($instance['category']) : self::DEFAULT_CATEGORY;

		return $instance;
	}

	public function in_widget_form( $widget, &$return, $instance )
	{		
		if( get_class( $widget ) == __CLASS__ )
		{
			$category = isset($instance['category']) ? intval($instance['category']) : self::DEFAULT_CATEGORY;
			
			$multimedia_category = get_category_by_slug('multimedia');
			$categories = get_categories('parent=0&exclude='.$multimedia_category->cat_ID); ?>

			<p>
				<label for="<?php echo $widget->get_field_id( 'category' );?>">Show posts from category:</label>
				<select id="<?php echo $widget->get_field_id( 'category' );?>" name="<?php echo $widget->get_field_name( 'category' );?>">
					<option value="0" <?php selected( '0', $category ); ?>>All</option>
					<option value="-1" <?php selected('-1', $category ); ?>>Category's Default</option>
					<?php foreach( $categories AS $cat ) : ?>
					<option value="<?php echo $cat->cat_ID;?>" <?php selected( $cat->cat_ID, $category ); ?>>
						<?php echo $cat->name;?>
					</option>
					<?php endforeach;?>
				</select>
			</p>
			
		<?php
		}
	}

	private function _get_current_category_id()
	{
		global $wp_query;
		$category = $wp_query->get_queried_object();
		return $category->cat_ID ;

	}
	function widget($args, $instance)
	{				
		//$cache = wp_cache_get('widget_recent_posts', 'widget');

		if ( !is_array($cache) )
			$cache = array();
				
		if ( isset($cache[$args['widget_id']]) ) {			
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		 if (is_single()){				
			$qpost = get_queried_object();
			$cat = get_the_category( $qpost->ID );
			$categoryid = $cat[0]->cat_ID;
			$categoryname = $cat[0]->cat_name;
		}



		$title = apply_filters('widget_title', empty($instance['title']) ? 'Recent Posts' :  $categoryname .  ' ' .  $instance['title'] , $instance, $this->id_base);
		if ( ! $number = absint( $instance['number'] ) )
 			$number = 5;

		$category_id    = (int) $instance['category'];
		if (is_single()){		
			$category    = ( $category_id == -1 ) ? $categoryid : $category_id;
		}else{
			$category    = ( $category_id == -1 ) ? $this->_get_current_category_id() : $category_id;
		}


			
			 
		$pargs = array('posts_per_page' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'cat' => $category, 'no_found_rows' => true );				


				


		
		$r = new WP_Query($pargs);

		if ( $r->have_posts() ) :
			$counter = 0;
			echo $before_widget;
			if ( $title ) echo $before_title . $title . $after_title;
	
			while ($r->have_posts()) :

				$r->the_post();

				$counter++;

				$small_story_args['is_first'] = ( $counter == 1 );
				$small_story_args['is_last']  = ( $counter == $number );

				$story = LM_story::get_story( get_post($post) );

				if( is_category( 'opinion' ) ) $story = new Control_author_story( $story, false );
				else            					 $story = new Control_small_story( $story );

				$story->display( $small_story_args );
			endwhile;

			echo $after_widget;
			// Reset the global $the_post as this query will have stomped on it
			wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_posts', $cache, 'widget');
	}
}

add_filter('widget_links_args', 'exp_blogroll_args');
function exp_blogroll_args( $blogroll_args )
{
	$blogroll_args['category_before'] = '<div id="blogroll" class="widget"><div class="content">';
	$blogroll_args['title_before']    = '<h4>';
	$blogroll_args['title_after']     = '</h4>';
	$blogroll_args['category_after']  = '</div></div>';	
	$blogroll_args['class']           = 'widget';
	
	return $blogroll_args;
}

/*
 * The code below adds the link to the Tribune T2 Magazine
 */
add_action("widgets_init", array('Tribune_T2','init'));
class Tribune_T2 extends WP_Widget
{
	function __construct()
	{
		parent::__construct('Tribune_T2', 'Tribune T2 Widget', array('description' => 'Displays the link to the T2 Magazine'));
	}
    
	public static function init()
	{
		register_widget('Tribune_T2');
	}

	function widget($args,$instance)
	{
		$days_to_show_widget = array( 'saturday', 'sunday', 'monday' );
		$default_timezone = date_default_timezone_get();
		date_default_timezone_set('Asia/Karachi');
		$current_day = strtolower( date('l') );
		$show_widget = false;
		
		if( in_array($current_day,$days_to_show_widget) ) $show_widget = true;
		if( $current_day == 'saturday' && date('G') < 9 ) $show_widget = false; // Magazine doesnt show up before 9AM on saturdays
		
		if( $show_widget ) : 
		?>
		<div id="t2-widget" class="widget button">
			<a href="/t2/">
				<img alt="" src="<?php echo get_template_directory_uri() ?>/img/t2-banner.jpg" width="305" height="64" />
			</a>
		</div>
		<?php
		endif;
		date_default_timezone_set($default_timezone); 
	}
}


/*
 * The code below adds the link to the Express Tribune Survey
 */
add_action("widgets_init", array('Tribune_Survey','init'));
class Tribune_Survey extends WP_Widget
{
	function __construct()
	{
		parent::__construct('Tribune_Survey', 'Tribune Survey Link', array('description' => 'Displays the link to the Express Tribune Survey'));
	}
    
	public static function init()
	{
		register_widget('Tribune_Survey');
	}

	function widget($args,$instance)
	{
		?>
		<div id="survey-widget" class="widget button">
			<a href="http://bit.ly/tribune-survey" target="_blank">
				<img alt="" src="<?php echo get_template_directory_uri() ?>/img/survey-banner.jpg" width="305" height="64" />
			</a>
		</div>
		<?php
	}
}


/*
 * The code below adds the link to the Express Tribune Subscribe
 */
add_action("widgets_init", array('Tribune_Subscribe','init'));
class Tribune_Subscribe extends WP_Widget
{
	function __construct()
	{
		parent::__construct('Tribune_Subscribe', 'Tribune Subscribe Link', array('description' => 'Displays the link to the Express Tribune Subscribe'));
	}
    
	public static function init()
	{
		register_widget('Tribune_Subscribe');
	}

	function widget($args,$instance)
	{
		?>
		<div id="subscribe-widget" class="widget button">
			<a href="<?php echo home_url('susbcribe/'); ?>">
				<img alt="" src="<?php echo get_template_directory_uri() ?>/img/subscribe-banner.gif" width="305" height="80" />
			</a>
		</div>
		<?php
	}
}


/*
 * The code below adds the link to the Gamechangers page
 */

add_action("widgets_init", array('Gamechangers_Widget','init') );
class Gamechangers_Widget extends WP_Widget
{
	function __construct()
	{
		parent::__construct( false, 'Gamechangers Widget', array('description' => 'Displays the link to the Gamechangers Page.'));
	}

	public static function init()
	{
		register_widget( __CLASS__ );
	}

	function widget($args,$instance)
	{
		?>
		<div class="widget button clearfix">
			<a href="<?php echo home_url('gamechangers');?>">
				<img class="left" alt="Tribune’s Gamechangers 2011" src="<?php echo get_template_directory_uri() ?>/img/game-changer-button.png" />
			</a>
		</div>
		<?php
	}
}

add_action("widgets_init", array('Political_Figures_Widget','init') );
class Political_Figures_Widget extends WP_Widget
{
	function __construct()
	{
		parent::__construct( false, 'Political Figures Widget', array('description' => 'Displays Political Figures.'));
	}

	public static function init()
	{
		register_widget( __CLASS__ );
	}

	function widget($args,$instance)
	{
		exp_load_widget_file("political-figures");
	}
}

add_action("widgets_init", array('Political_Parties_Position_Widget','init') );
class Political_Parties_Position_Widget extends WP_Widget
{
	function __construct()
	{
		parent::__construct( false, 'Political Parties Position Widget', array('description' => 'Displays Parties Position Figures.'));
      if ( is_active_widget(false, false, $this->id_base) )
      {
         wp_register_script('jsapi', 'https://www.google.com/jsapi',  array(), false, true);
         wp_register_script('political-parties-position', get_template_directory_uri().'/js/political-parties-position.js',  array('jsapi'), '1.0', true );
         wp_enqueue_script('political-parties-position');
         
         
         $parties  = array( "ppp", "pml-n", "pti",  "pml-q", "mqm", "anp", "ji", "jui-f" );

         $parties_position = get_option( 'parties_position', array() );
         if( is_serialized($parties_position) ) $parties_position = unserialize ($parties_position);

         $params = array();
         $params[] = array( 'Parties', 'Seats' );
         
         foreach ( $parties as $party )         
            $params[] = array( strtoupper($party), isset($parties_position['Federal'][$party]) ? (int)$parties_position['Federal'][$party] : 0 );
         wp_localize_script( 'political-parties-position', 'Parties_Data', $params );
      }      
	}

	public static function init()
	{
		register_widget( __CLASS__ );
	}

	function widget($args,$instance)
	{      
		exp_load_widget_file("political-parties-position");
	}
}

add_action("widgets_init", array('Elections2013_MRec_Banner','init'));
class Elections2013_MRec_Banner extends WP_Widget
{
	function __construct()
	{
		parent::__construct( false , 'Elections2013 MRec Banner Widget', array('description' => 'Displays Elections2013 mrec banner'));
	}
    
	public static function init()
	{
		register_widget(__CLASS__);
	}
	
	function widget($args,$instance)
	{
		?>
		<div class="widget button clearfix" id="election2013-mrec" >
			<a href="<?php echo esc_url( home_url() ) ?>/elections2013/">
				<img class="left" alt="ePaper" src="<?php echo get_template_directory_uri() ?>/img/ads/elections2013.png?v=0.1" />
			</a>
		</div>
		<?php 
	}
}
