<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

define( 'EXP_TEMPLATES_PATH', trailingslashit(TEMPLATEPATH) . 'template-parts/layouts/' );

function exp_get_template_path($path='')
{
	global $wp_query;

	// setting up main file name and path
	$root_file_path = EXP_TEMPLATES_PATH . 'index.php' ;

	// setup the wp_query vars based on the request uri
	_exp_handle_wp_request();

	// if is home page request
	if( is_home() )
	{
		$file_path = EXP_TEMPLATES_PATH. 'home/home-index.php';
		return (file_exists($file_path)) ? $file_path : $root_file_path;
	}

	// is the request for a single post page
	if( is_single() && FALSE === is_feed() ) return EXP_TEMPLATES_PATH . 'single.php';   

	// is the request for the search page
	if( is_search() ) return EXP_TEMPLATES_PATH . 'search.php';

	// is the request for the author page
	if( is_author() )	return EXP_TEMPLATES_PATH . ( is_feed() ? 'author-feed-rss2.php' : 'author.php');

	// is the request for a category
	if( is_category() )
	{
		$category     = $wp_query->get_queried_object();
		$sub_category = false;

		if( $category->parent )
		{
			$sub_category = $category;
			$category     = $category->parent;
		}

		// setup the file names
		$sub_category_file      = "{$category->slug}/{$sub_category->slug}/{$sub_category->slug}-";
		$category_file          = "{$category->slug}/{$category->slug}-";
		$sub_category_root_file = "subsections-";
		$category_root_file     = "sections-";
		$root_file              = "";

		if( is_feed() )
		{
			$feed_type = get_query_var( 'feed' );
			
			$sub_category_file      = EXP_TEMPLATES_PATH . $sub_category_file . "feed-{$feed_type}.php";
			$category_file          = EXP_TEMPLATES_PATH . $category_file . "feed-{$feed_type}.php";
			$sub_category_root_file = EXP_TEMPLATES_PATH . $sub_category_root_file . "feed-{$feed_type}.php";
			$category_root_file     = EXP_TEMPLATES_PATH . $category_root_file . "feed-{$feed_type}.php";
			$root_file              = EXP_TEMPLATES_PATH . "feed-{$feed_type}.php";
		}
		else
		{
			$sub_category_file      = EXP_TEMPLATES_PATH . $sub_category_file . "index.php";
			$category_file          = EXP_TEMPLATES_PATH . $category_file . "index.php";
			$sub_category_root_file = EXP_TEMPLATES_PATH . $sub_category_root_file . "index.php";
			$category_root_file     = EXP_TEMPLATES_PATH . $category_root_file . "index.php";
			$root_file              = $root_file_path;
		}

		// is the request for a subcategory
		if( $sub_category )
		{
			// if a template exists for the sub-category show it
			if( file_exists( $sub_category_file ) ) return $sub_category_file;

			// if a default template exists for the sub-categories show it
			if( file_exists( $sub_category_root_file ) ) return $sub_category_root_file;
		}

		// if a template file exists for the category show it
		if( file_exists( $category_file ) ) return $category_file;

		// if a default template exists for the categories show it
		if( file_exists( $category_root_file ) ) return $category_root_file;

		return $root_file;
	}
	
	// is the request for a trend page
	if( is_tag() && FALSE === is_feed() )
	{
		$tag = $wp_query->get_queried_object();

		// if a template file exists for this particular trend show it
		$file = EXP_TEMPLATES_PATH . "trends/{$tag->slug}/{$tag->slug}-index.php";
		if( file_exists( $file ) ) return $file;

		// if a template file exists for the default trends template show it
		$file = EXP_TEMPLATES_PATH . 'trends/trends-index.php';
		if( file_exists( $file ) ) return $file;
	}

	// is the request for a generic page
	if( is_page() )
	{
		$page = $wp_query->get_queried_object();
		
		if( is_feed() )
		{
			$feed_type = get_query_var( 'feed' );
			$file = EXP_TEMPLATES_PATH . "{$page->post_name}/{$page->post_name}-feed-{$feed_type}.php";
		}
		else
		{
			// if a template file exists for this particular page show it
			$file = EXP_TEMPLATES_PATH . "{$page->post_name}/{$page->post_name}-index.php";
		}

		if( file_exists( $file ) )return $file;
		else return EXP_TEMPLATES_PATH . '404.php';
	}

	// is the request for a generic feed
	if( is_feed() )
	{
		$feed_type = get_query_var( 'feed' );
		
		return EXP_TEMPLATES_PATH . "feed-{$feed_type}.php";
	}
	
	// if we have come all the way here that means this is a 404 request	
	return EXP_TEMPLATES_PATH . '404.php';
}

function exp_template_file($template, $data = null)
{
	global $wp_query;
	
	if( is_home() )
	{
		$file_path = EXP_TEMPLATES_PATH . "home/home-{$template}.php";
		if( file_exists($file_path) ) return include $file_path;
	}

	if( is_category() )
	{
		$category     = $wp_query->get_queried_object();
		$sub_category = false;

		if( $category->parent )
		{
			$sub_category = $category;
			$category     = $category->parent;
		}

		// is the request for a subcategory
		if( $sub_category )
		{ 	
			if( $sub_category->slug == 'slideshows')
		{
			$file = EXP_TEMPLATES_PATH . "multimedia/slideshows/slideshows-index.php";
			if( file_exists( $file ) ) return include( $file );
		} 	elseif( $sub_category->slug == 'videos')
		{
			$file = EXP_TEMPLATES_PATH . "multimedia/videos/videos-index.php";
			if( file_exists( $file ) ) return include( $file );
		}else{
			// if a template file exists for the sub-category show it
			$file = EXP_TEMPLATES_PATH . "{$category->slug}/{$sub_category->slug}/{$sub_category->slug}-{$template}.php";
			if( file_exists( $file ) ) return include( $file );
		}
			// since template file for subcategory does not exist hence show default subcategory template
			$file = EXP_TEMPLATES_PATH . "subsections-{$template}.php";
			if( file_exists( $file ) ) return include( $file );
		}

		if( $category->slug == 'multimedia')
		{
			$file = EXP_TEMPLATES_PATH . "multimedia/multimedia-index.php";
			if( file_exists( $file ) ) return include( $file );
		}
		

		// if a template file exists for the category show it
		$file = EXP_TEMPLATES_PATH . "{$category->slug}/{$category->slug}-{$template}.php";
		if( file_exists( $file ) ) return include( $file );

		// since template file for category does not exist hence show default category template
		$file = EXP_TEMPLATES_PATH . "sections-{$template}.php";
		if( file_exists( $file ) ) return include( $file );
	}

	if( is_tag() )
	{
		$tag = $wp_query->get_queried_object();

		// if a template file exists for this particular trend show it
		$file = EXP_TEMPLATES_PATH . "trends/{$tag->slug}/{$tag->slug}-{$template}.php";
		if( file_exists( $file ) ) return include( $file );

		// if a template file exists for the default trends template show it
		$file = EXP_TEMPLATES_PATH . "trends/trends-{$template}.php";
		if( file_exists( $file ) ) return include( $file );
	}

	// is the request for a generic page
	if( is_page() )
	{
		$page = $wp_query->get_queried_object();

		// if a template file exists for this particular page show it
		$file = EXP_TEMPLATES_PATH . "{$page->post_name}/{$page->post_name}-{$template}.php";
		if( file_exists( $file ) ) return include( $file );
	}

	$file = EXP_TEMPLATES_PATH . "{$template}.php";
	if( file_exists( $file ) ) return include( $file );

	return exp_die( 'Error loading '.$template.' '.__LINE__ );
}

function exp_load_widget_file($template, $data = null)
{
	$file_path = TEMPLATEPATH . "/template-parts/widgets/{$template}.php";

	if( file_exists($file_path) ) include( $file_path );
}

function get_group($template, $data = null)
{
	$file_path = TEMPLATEPATH . "/template-parts/groups/{$template}.php";

	if( file_exists( $file_path ) )
	{
		if( is_array( $data ) ) extract( $data );

		include( $file_path );
	}
}

function get_control($template, $data = null, $slideshow = null)
{
	$file_path = TEMPLATEPATH . "/template-parts/controls/{$template}.php";

	if( file_exists( $file_path ) )
	{
		if( is_array( $data ) ) extract( $data );
		
		include( $file_path );
	}
}

function exp_header()
{
	exp_template_file( 'header' );
}

function exp_footer()
{
	exp_template_file( 'footer' );
}

function exp_primary($tpl_type='')
{
	$tpl_type = trim( $tpl_type );
	$tpl_name = ( $tpl_type == '' ) ? 'primary' : "{$tpl_type}-primary" ;
	
	exp_template_file( $tpl_name );
}

function exp_sidebar($tpl_type = '')
{
	$tpl_type = trim( $tpl_type );
	$tpl_name = ( $tpl_type == '' ) ? 'sidebar' : "{$tpl_type}-sidebar" ;
	
	exp_template_file( $tpl_name );
}

function exp_comments()
{
	exp_template_file( 'comments' );
}

function exp_threaded_comment( $comment, $args = array(), $depth )
{
	// if the comment is not approved skip displaying it
	if ( $comment->comment_approved == '1')
	{
		global $post;
		$GLOBALS['comment'] = $comment;
		$data = array(
			'args'  => $args,
			'depth' => $depth,
		);
		exp_template_file( 'threaded', $data );
	}
}

function exp_comment($data = null)
{
	exp_template_file( 'comment', $data );
}

function exp_die($str = '')
{
	if( empty( $str ) ) return;

	exp_template_file( 'exit', $str );
	die();
}

function _exp_handle_wp_request()
{
	global $wp_query;

	$term_slug   = get_query_var( 'term_slug' );
	$term_parent = get_query_var( 'term_parent' );	

	// if the term slug is not set that means this request is neither a category nor a tag request	
	if( false == $term_slug ) return;

	$wp_query->is_single   = false;
	$wp_query->is_404      = false;
	$wp_query->is_home     = false;
	$wp_query->is_category = false;
	$wp_query->is_tag      = false;

	// if the term slug is that of the home page
	if( $term_slug == "home" )
	{		
		$wp_query->is_home = true;
		return;
	}

	// is the request for a category
	$category = get_category_by_slug( $term_slug );
	if( $category )
	{
		if( $category->parent > 0 )
		{
			$parent_category = get_category_by_slug( $term_parent );
			if( $parent_category && $parent_category->cat_ID == $category->parent )	$wp_query->is_category = true;			
		} else $wp_query->is_category = true;

		if( false !== $wp_query->is_category )
		{
			$wp_query->is_archive = true;

			$wp_query->set( 'cat', $category->cat_ID );
			$wp_query->set( 'category_name', $category->cat_name );

			$wp_query->queried_object    = $category;						
			$wp_query->queried_object_id = $category->cat_ID;
			if( $category->parent > 0 ) $wp_query->queried_object->parent = $parent_category;

			return;
		}
	}		
				
	// is the request for a generic page
	$file = EXP_TEMPLATES_PATH . "{$term_slug}/{$term_slug}-index.php";
	
	if( file_exists( $file ) )
	{
		$wp_query->set( 'pagename', $term_slug );
				
		$wp_query->is_page   = true;

		$page = new stdClass();
		$page->post_name = $term_slug;
		$page->slug      = $term_slug;		

		$wp_query->queried_object = $page;
		return;
	}

	// is the request for a tag
	$tag = get_term_by( 'slug', $term_slug, 'post_tag' );
	if( $tag )
	{
		$wp_query->set( 'tag_id', $tag->term_id );
		$wp_query->set( 'tag', $tag->name );

		$wp_query->is_tag            = true;
		$wp_query->is_archive        = true;

		$wp_query->queried_object    = $tag;
		$wp_query->queried_object_id = $tag->term_id;

		return;
	}

	$wp_query->is_404 = true;	
}