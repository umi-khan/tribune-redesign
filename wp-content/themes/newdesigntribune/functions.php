<?php
/**
 * Twenty Sixteen functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

/**
 * Twenty Sixteen only works in WordPress 4.4 or later.
 */
include_once(dirname(__FILE__) . '/express-core/utility.php');
include_once(dirname(__FILE__) . '/controls.php');
include_once(dirname(__FILE__) . '/widgets.php');
include_once(dirname(__FILE__) . '/menu.php');
//include_once(dirname(__FILE__) . '/story_images.php');
//include_once(dirname(__FILE__) . '/story_videos.php');
define( 'EXP_TEMPLATES_PATH', trailingslashit(TEMPLATEPATH) . 'template-parts/layouts/' );
if ( version_compare( $GLOBALS['wp_version'], '4.4-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
}

if ( ! function_exists( 'twentysixteen_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * Create your own twentysixteen_setup() function to override in a child theme.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Twenty Sixteen, use a find and replace
	 * to change 'twentysixteen' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'twentysixteen', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for custom logo.
	 *
	 *  @since Twenty Sixteen 1.2
	 */
	add_theme_support( 'custom-logo', array(
		'height'      => 240,
		'width'       => 240,
		'flex-height' => true,
	) );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 1200, 9999 );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'twentysixteen' ),
		'brand'  => __( 'Top Brand Menu', 'twentysixteen' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
		'status',
		'audio',
		'chat',
	) );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/editor-style.css', twentysixteen_fonts_url() ) );

	// Indicate widget sidebars can use selective refresh in the Customizer.
	add_theme_support( 'customize-selective-refresh-widgets' );
}
endif; // twentysixteen_setup
add_action( 'after_setup_theme', 'twentysixteen_setup' );

/**
 * Sets the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'twentysixteen_content_width', 840 );
}
add_action( 'after_setup_theme', 'twentysixteen_content_width', 0 );

/**
 * Registers a widget area.
 *
 * @link https://developer.wordpress.org/reference/functions/register_sidebar/
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_widgets_init() {
register_sidebar( array(
		'name'          => __( 'Sidebar', 'twentysixteen' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar.', 'twentysixteen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Content Bottom 1', 'twentysixteen' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Appears at the bottom of the content on posts and pages.', 'twentysixteen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Category Page', 'twentysixteen' ),
		'id'            => 'sidebar-3',
		'description'   => __( 'Appears at the bottom of the content on posts and pages.', 'twentysixteen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Leaderboard Top', 'twentysixteen' ),
		'id'            => 'leaderboard-top',
		'description'   => __( 'Appears at header of the content on posts and pages.', 'twentysixteen' ),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	) );
	register_sidebar( array(
		'name'          => __( 'Leaderboard BTF1', 'twentysixteen' ),
		'id'            => 'leaderboard-btf1',
		'description'   => __( 'Appears at the 1st middle of the content on home page.', 'twentysixteen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Leaderboard BTF2', 'twentysixteen' ),
		'id'            => 'leaderboard-btf2',
		'description'   => __( 'Appears at the 2nd middle of the content on home page.', 'twentysixteen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Leaderboard BTF3', 'twentysixteen' ),
		'id'            => 'leaderboard-btf3',
		'description'   => __( 'Appears at the 3rd middle of the content on home page.', 'twentysixteen' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar(
		array(
			'name' => 'Subsection Default Middle Sidebar',
			'id'            => 'sidebar-4',
			'before_widget' => '<div id="%1$s" class="clearfix widget %2$s">',
			'after_widget' => '</div></div>',
			'before_title' => '<h4 class="title">',
			'after_title' => '</h4><div class="content">'
		)
	);
}
add_action( 'widgets_init', 'twentysixteen_widgets_init' );

if ( ! function_exists( 'twentysixteen_fonts_url' ) ) :
/**
 * Register Google fonts for Twenty Sixteen.
 *
 * Create your own twentysixteen_fonts_url() function to override in a child theme.
 *
 * @since Twenty Sixteen 1.0
 *
 * @return string Google fonts URL for the theme.
 */
function twentysixteen_fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'latin,latin-ext';

	/* translators: If there are characters in your language that are not supported by Merriweather, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Merriweather font: on or off', 'twentysixteen' ) ) {
		$fonts[] = 'Merriweather:400,700,900,400italic,700italic,900italic';
	}

	/* translators: If there are characters in your language that are not supported by Montserrat, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Montserrat font: on or off', 'twentysixteen' ) ) {
		$fonts[] = 'Montserrat:400,700';
	}

	/* translators: If there are characters in your language that are not supported by Inconsolata, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Inconsolata font: on or off', 'twentysixteen' ) ) {
		$fonts[] = 'Inconsolata:400';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $fonts ) ),
			'subset' => urlencode( $subsets ),
		), 'https://fonts.googleapis.com/css' );
	}

	return $fonts_url;
}
endif;

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'twentysixteen_javascript_detection', 0 );

/**
 * Enqueues scripts and styles.
 *
 * @since Twenty Sixteen 1.0
 */
function twentysixteen_scripts() {
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'twentysixteen-fonts', twentysixteen_fonts_url(), array(), null );

	// Add Genericons, used in the main stylesheet.
	wp_enqueue_style( 'genericons', get_template_directory_uri() . '/genericons/genericons.css', array(), '3.4.1' );

	// Theme stylesheet.
	wp_enqueue_style( 'twentysixteen-style', get_stylesheet_uri() );

	// Load the tribune css.
	wp_enqueue_style( 'twentysixteen-tribune', get_template_directory_uri() . '/css/master.css');
	wp_enqueue_style( 'twentysixteen-responsive', get_template_directory_uri() . '/css/responsive.css');
	wp_enqueue_style( 'twentysixteen-carousel', get_template_directory_uri() . '/css/carousel.css');
	wp_enqueue_style( 'twentysixteen-slideshow', get_template_directory_uri() . '/css/slideshow.css');
	wp_enqueue_style( 'twentysixteen-slideshow-cycle', get_template_directory_uri() . '/css/slideshow-cycle.css');
	wp_enqueue_style( 'twentysixteen-wmd', get_template_directory_uri() . '/css/wmd.css');
	wp_enqueue_style( 'twentysixteen-socialbar', get_template_directory_uri() . '/css/socialbar.css');
	wp_enqueue_style( 'twentysixteen-fontawesome', get_template_directory_uri() . '/css/font-awesome.css');
	wp_enqueue_style( 'twentysixteen-navigation', get_template_directory_uri() . '/css/navigation.css');
	


	

	// Load the Internet Explorer specific stylesheet.
	wp_enqueue_style( 'twentysixteen-ie', get_template_directory_uri() . '/css/ie.css', array( 'twentysixteen-style' ), '20160412' );
	wp_style_add_data( 'twentysixteen-ie', 'conditional', 'lt IE 10' );

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'twentysixteen-ie8', get_template_directory_uri() . '/css/ie8.css', array( 'twentysixteen-style' ), '20160412' );
	wp_style_add_data( 'twentysixteen-ie8', 'conditional', 'lt IE 9' );

	// Load the Internet Explorer 7 specific stylesheet.
	wp_enqueue_style( 'twentysixteen-ie7', get_template_directory_uri() . '/css/ie7.css', array( 'twentysixteen-style' ), '20160412' );
	wp_style_add_data( 'twentysixteen-ie7', 'conditional', 'lt IE 8' );

	wp_enqueue_script( 'carousel', get_template_directory_uri() . '/js/carousel.js', array(), '20160412', true );

	// Load the html5 shiv.
	wp_enqueue_script( 'twentysixteen-html5', get_template_directory_uri() . '/js/html5.js', array(), '3.7.3' );
	wp_script_add_data( 'twentysixteen-html5', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'twentysixteen-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20160412', true );

	wp_enqueue_script( 'socialshare', get_template_directory_uri() . '/js/socialshare.min.js', array(), '20160412', true );
	wp_enqueue_script( 'cycle2', get_template_directory_uri() . '/js/jquery.cycle2.js', array(), '20160412', true );
	wp_enqueue_script( 'rad-wrapper', get_template_directory_uri() . '/js/rad.min.js', array(), '20160412', true );
	wp_enqueue_script( 'slideshow', get_template_directory_uri() . '/js/slideshow.js', array(), '20160412', true );
	wp_enqueue_script( 'fb-sdk', get_template_directory_uri() . '/js/fb-sdk.js', array(), '20160412', true );

	
	

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'twentysixteen-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20160412' );
	}

	wp_enqueue_script( 'twentysixteen-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20160412', true );

	wp_localize_script( 'twentysixteen-script', 'screenReaderText', array(
		'expand'   => __( 'expand child menu', 'twentysixteen' ),
		'collapse' => __( 'collapse child menu', 'twentysixteen' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'twentysixteen_scripts' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @since Twenty Sixteen 1.0
 *
 * @param array $classes Classes for the body element.
 * @return array (Maybe) filtered body classes.
 */
function twentysixteen_body_classes( $classes ) {
	// Adds a class of custom-background-image to sites with a custom background image.
	if ( get_background_image() ) {
		$classes[] = 'custom-background-image';
	}

	// Adds a class of group-blog to sites with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of no-sidebar to sites without active sidebar.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'twentysixteen_body_classes' );

/**
 * Converts a HEX value to RGB.
 *
 * @since Twenty Sixteen 1.0
 *
 * @param string $color The original color, in 3- or 6-digit hexadecimal form.
 * @return array Array containing RGB (red, green, and blue) values for the given
 *               HEX code, empty array otherwise.
 */
function twentysixteen_hex2rgb( $color ) {
	$color = trim( $color, '#' );

	if ( strlen( $color ) === 3 ) {
		$r = hexdec( substr( $color, 0, 1 ).substr( $color, 0, 1 ) );
		$g = hexdec( substr( $color, 1, 1 ).substr( $color, 1, 1 ) );
		$b = hexdec( substr( $color, 2, 1 ).substr( $color, 2, 1 ) );
	} else if ( strlen( $color ) === 6 ) {
		$r = hexdec( substr( $color, 0, 2 ) );
		$g = hexdec( substr( $color, 2, 2 ) );
		$b = hexdec( substr( $color, 4, 2 ) );
	} else {
		return array();
	}

	return array( 'red' => $r, 'green' => $g, 'blue' => $b );
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images
 *
 * @since Twenty Sixteen 1.0
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function twentysixteen_content_image_sizes_attr( $sizes, $size ) {
	$width = $size[0];

	840 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 62vw, 840px';

	if ( 'page' === get_post_type() ) {
		840 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	} else {
		840 > $width && 600 <= $width && $sizes = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 61vw, (max-width: 1362px) 45vw, 600px';
		600 > $width && $sizes = '(max-width: ' . $width . 'px) 85vw, ' . $width . 'px';
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'twentysixteen_content_image_sizes_attr', 10 , 2 );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for post thumbnails
 *
 * @since Twenty Sixteen 1.0
 *
 * @param array $attr Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size Registered image size or flat array of height and width dimensions.
 * @return string A source size value for use in a post thumbnail 'sizes' attribute.
 */
function twentysixteen_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( 'post-thumbnail' === $size ) {
		is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 984px) 60vw, (max-width: 1362px) 62vw, 840px';
		! is_active_sidebar( 'sidebar-1' ) && $attr['sizes'] = '(max-width: 709px) 85vw, (max-width: 909px) 67vw, (max-width: 1362px) 88vw, 1200px';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'twentysixteen_post_thumbnail_sizes_attr', 10 , 3 );

/**
 * Modifies tag cloud widget arguments to have all tags in the widget same font size.
 *
 * @since Twenty Sixteen 1.1
 *
 * @param array $args Arguments for tag cloud widget.
 * @return array A new modified arguments.
 */
function twentysixteen_widget_tag_cloud_args( $args ) {
	$args['largest'] = 1;
	$args['smallest'] = 1;
	$args['unit'] = 'em';
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'twentysixteen_widget_tag_cloud_args' );





function exp_single_comments_link( $story_id , $echo = true, $btf = false )
{
	$result = "";
	if(ctype_digit($story_id))
	{
		$story = get_post($story_id);
		if($story->comment_status == 'open')
		{
			$story_permalink = get_permalink($story_id);
			$template_url = get_template_directory_uri();
			$comment_title = 'Comments';
			if(isset($story->comment_count) && !empty($story->comment_count))
				$comment_title .= ' ('.$story->comment_count.')';
    		$result = '<a class="comment-link" href="'.$story_permalink.'#comments" title="'.$comment_title.'"><img src="'.$template_url.'/img/comment-18'.( $btf !== false ? '-btf' : '').'.gif?v=1.2" alt="" /><span>'.$comment_title.'</span></a>';
		}
	}
	
	if( $echo )	echo $result;
	else return $result;
}

function exp_single_addthis_button( $story_ID = null, $permalink = null, $title = null, $button_text = false, $btf = false )
{
	$title = esc_html($title);
	$addthis_button_id = '';
	$addthis_custom_url_title = '';
	$button_ID = '';
	$template_url = get_template_directory_uri();
	if(($story_ID != null && $story_ID != false) && ($permalink != null && $permalink != false ) )
	{
		$button_ID = 'addthis_button_'.$story_ID.'_'.rand();
		$addthis_button_id .= 'id="'.$button_ID.'" ';
		$addthis_script = '<script type="text/javascript"> jQuery(window).load(function(){ addthis.button("#'.$button_ID.'", {}, {url: "'.$permalink.'", title: "'.$title.'"}); }); </script>';
	}
	$addthis_button_html = '<a '.$addthis_button_id.'href="http://www.addthis.com/bookmark.php?v=250" class="share-this" title="Share">';
	$addthis_button_html .= '<img src="'.$template_url.'/img/mis-share'.( false !== $btf ? '-btf' : '' ) .'.gif?v=1.0" alt="" />';
	//if( $button_text ) $addthis_button_html .= sprintf( '<span>%s</span>' , $button_text );
	$addthis_button_html .= '</a>';
	$addthis_button_html .= $addthis_script;
	
	echo $addthis_button_html;
}

add_filter( 'next_posts_link_attributes', 'exp_next_posts_link_attributes' );
add_filter( 'previous_posts_link_attributes', 'exp_previous_posts_link_attributes' );

function exp_next_posts_link_attributes( $attr )
{
	if( !is_home() || !is_single() ) $attr .=  ' class="next"';
	return $attr;
}
function exp_previous_posts_link_attributes( $attr )
{
	if( !is_home() || !is_single() ) $attr .=  ' class="prev"';
	return $attr;
}

function exp_paginate_stories_links($args = array())
{
    global $posts_per_page, $paged, $wp_query;
	 
	 if (!$paged ) $paged = 1;

	$defaults = array(
		'seperator'          => ' | ',
		'stories_per_page'   => $posts_per_page,
		'found_stories'      => $wp_query->found_posts,
		'prev_text'          => '&laquo; Previous',
		'next_text'          => 'Next &raquo;',
		'before_html'        => '<div class="pagination">',
		'after_html'         => '</div>',
		'current_page_class' => 'active',
		'show_each_side'     => 5,
		'echo'               => true,
		'explainer'          => true,
		'show_nextprev_link' => true,
		//'format'             => '/page-%#%',
	);

	 $args = wp_parse_args( $args, $defaults );

	 $prev_text = $args['prev_text'];
	 $next_text = $args['next_text'];

	 $before_html = $args['before_html'];
	 $after_html = $args['after_html'];

	 $current_page_class = $args['current_page_class'];
	 $seperator = $args['seperator'];
	 $show_each_side = $args['show_each_side'];

	 $explainer = $args['explainer'];

	 echo $before_html;

	 if( false !== $args['show_nextprev_link'] )
	 {
		previous_posts_link($prev_text);
		next_posts_link($next_text, 0);
	 }
	 
    $found_stories = $args['found_stories'];
	 $stories_per_page = $args['stories_per_page'];

	 $links = array();
    $max_num_pages = ceil($found_stories / $stories_per_page);

	 $cnt = 1;
	 if($paged > ($show_each_side+1))
	 {
		 $cnt =  $paged - $show_each_side;
		 printf('<a href="%s"> <img src="%s/img/pagination-previous.gif?v=1.0" alt="<" /> </a>', get_pagenum_link($cnt-1), get_template_directory_uri() );
	 }
	 if ($max_num_pages > 1) {
		 $max = $paged+$show_each_side;
		 if($max > $max_num_pages)
			$max = $max_num_pages;

       for ($cnt; $cnt <= $max; $cnt++) {
			  if(($current_page_class && $paged == $cnt))
				  $link = sprintf('<span class="current">%s</span>',$cnt);
			  else
				  $link = sprintf('<a href="?page=%s" class="page-numbers">%s</a>', $cnt,$cnt);

			  $links[] = $link;
       }
       echo join($seperator, $links);
		 if($max < $max_num_pages)
			 printf('<a href="%s"> <img src="%s/img/pagination-next.gif?v=1.0" alt=">" /> </a>', get_pagenum_link($max+1), get_template_directory_uri() );
		 
    }
	 echo $after_html;

	if($explainer)
	{
		$first_post_on_page = ($stories_per_page * ($paged-1))+1;
		$last_post_on_page = $stories_per_page * $paged;
		if($last_post_on_page > $found_stories)
			$last_post_on_page = $found_stories;
		printf('<p class="explainer">%d-%d of %d</p>', $first_post_on_page, $last_post_on_page, $found_stories);
	}
}

add_filter( 'get_pagenum_link', 'exp_get_pagenum_link' );
function exp_get_pagenum_link( $link )
{	
	if( preg_match('/.*\/(slideshows\/(.+?)\/page)/i', $link, $matches) )
		$link = str_replace($matches[1], "slideshows/page", $link );
	else if ( preg_match('/.*\/(videos\/(.+?)\/page)/i', $link, $matches) )
		$link = str_replace($matches[1], "videos/page", $link );
	return $link;
}


/*---- video front end -------------*/

    function video_link($post_ID){
    /* For video id */
    global $wpdb, $post;
    $myrows = $wpdb->get_results("SELECT meta_value  FROM `wp_postmeta` WHERE `post_id` = $post->ID and meta_key='_vm_default_video'" );
    foreach ($myrows as $key => $object) {
        $last = $object->meta_value;
    }
    $myrows1 = $wpdb->get_results("SELECT * FROM `wp_posts` WHERE `ID` = $last");
    foreach ($myrows1 as $key => $object) {
        echo $link = $object->guid;
    }
    return $link;
    }


function exp_display_pst_date($format="l, d M Y")
{
    echo date("l,d M Y");
}

function exp_list_categories( $parent_cat_id = 0, $categories = false, $echo = true )
{			

	$class_first = $class_last = $list_prefix = $list_suffix = '';
	
	if( false === $categories )
	{
		$categories   = get_categories( sprintf( 'parent=%d&orderby=id&hide_empty=0', $parent_cat_id ) );
		$class_first  = 'first';
		$class_last   = 'last';
		$list_prefix  = '<ul>';
		$list_suffix  = '</ul>';
	}
		
	if( empty ( $categories )) return false;
	
	$categories_count     = count($categories);
	$counter              = 0;
	$blank_top_categories = array();

	$class                = $class_first;
	$list                 = '';
	
	foreach ( $categories as $category )
	{				
		$cat_id               = $category->cat_ID;
		$is_top_category      = ( intval( $category->parent ) === $parent_cat_id );

		if( $is_top_category )
		{

			$list_categories = exp_list_categories( $cat_id, false, false );
			if( false === $list_categories )
			{				
				$blank_top_categories[] = $category;
				continue;
			}
		}
		
		$link    = sprintf( '<a href="%s" title="%s">%2$s</a>', get_category_link( $cat_id ) , $category->cat_name );
		
		$is_last = ( ++$counter == $categories_count );
		
		if( $is_last ) $class = $class_last;

		if ( $is_top_category )		
			$list .= sprintf( '<li class="%s %s categories"><h5>%s</h5>%s %s</li>%s' , $category->cat_name, $class, $link, $list_prefix, $list_categories,$list_suffix);
		else
			$list .= sprintf( '<li class="%s">%s</li>%s' , $class, $link , ( $is_last ? $list_suffix : '' ) );

		$class = '';
	}
	if ( !empty( $blank_top_categories ) )
	{		
        $column_count = count($blank_top_categories);
		
		if($column_count == 5){
			$atoZ 		= "<h5>A -- Z</h5>";
			$blog 		= '<li><a href="http://blogs.tribune.com.pk/" target="_blank" title="blog">Blogs</a></li>';
			$webChutney = '<li><a href="http://www.webchutney.pk/" target="_blank" title="WebChutney">WebChutney</a></li>';			
		}else{		
			$atoZ = $blog = $webChutney = '';
		}
		
		$list .= sprintf('<li class="categories">
									'.$atoZ.'
										<ul>
										'.$blog.'
											%s	
										'.$webChutney.'	
										</ul>
								</li>',
				  
				  exp_list_categories( false, $blank_top_categories , false )
				  );				 
	}

	if( false === $echo )
		return $list;

	echo $list;
}

function get_slide_image($postid)
{
    global  $image_manager, $video_manager;

        $image_manager = new IM_Manager( $postid);
        if( false === $image_manager->has_images() ){
            $video_manager = new VM_Manager( $postid );        
            $image['src'] = $video_manager->default_video->thumbnail->url;
            $image['isvideo'] = "true";
        }else{
            $image['src']= $image_manager->default_image->large->url;
            $image['isvideo'] = "false";
        } 
    return $image;
}

function exp_comments_link($story_id, $echo=true)
{
	$result = "";
	if(ctype_digit($story_id))
	{
		$story = get_post($story_id);
		if($story->comment_status == 'open')
		{
			$story_comments = "Comments";
    		$story_comments .= ($story->comment_count > 1) ? " ($story->comment_count)" : "";
    		$story_permalink = get_permalink($story_id);
    		
    		$result = ' <a class="comments" href="'.$story_permalink.'#comments">'.$story_comments.'</a>';
		}
	}
	
	if( $echo )	echo $result;
	else	return $result;
}

if (!function_exists('exp_addthis_button'))
{
	function exp_addthis_button($story_ID = null, $permalink = null, $title = null)
	{
		$title = htmlspecialchars($title);
		$link_shown = 'Share';
		$addthis_button_id = '';
		$addthis_custom_url_title = '';
		$button_ID = '';
		if(($story_ID != null && $story_ID != false) && ($permalink != null && $permalink != false ) )
		{
			$button_ID = 'addthis_button_'.$story_ID;
			$addthis_button_id .= 'id="'.$button_ID.'" ';
			$addthis_custom_url_title = '<script type="text/javascript"> jQuery(window).load(function(){ addthis.button("#'.$button_ID.'", {}, {url: "'.$permalink.'", title: "'.$title.'"}); }); </script>';
		}
		$addthis_button_html = '<a '.$addthis_button_id.'href="http://www.addthis.com/bookmark.php?v=250&amp;username=etribune" class="addthis_button">'.$link_shown.'</a>';
		$addthis_button_html .= $addthis_custom_url_title;
		
		echo $addthis_button_html;
	}
}

if (!function_exists('exp_get_email_link'))
{
	function exp_get_email_link( $post_or_id = false )
	{		
		$email_link = null;
		if ( is_plugin_active('wp-email/wp-email.php') )
		{
			global $post;

			if ( false === $post_or_id )
				$_post = $post;
			else
				$_post = is_numeric( $post_or_id ) ? get_post( $post_or_id ) : ( $post_or_id->post_details ? $post_or_id->post_details : $post_or_id );
			
			if( $_post )
			{				
				$permalink = get_permalink( $_post->ID );
				$email_link = str_replace( '/story/', '/email/', $permalink );
			}			
		}		
		return $email_link;
	}
}


add_filter( 'pre_option_upload_url_path', 'wpse_77960_upload_url' );

function wpse_77960_upload_url()
{
    return 'http://i1.tribune.com.pk/wp-content/uploads';
}








