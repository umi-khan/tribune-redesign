<?php
/*
Plugin Name: Permalink Rewrite Rules
Version: 1.0
Plugin URI: http://tribune.com.pk
Author: Express Media
Author URI: http://express.com.pk
Description: Modify the permalink structure according to custom requirements
*/

add_filter( 'post_link', 'pl_sanitize_post_link', 10, 3 );
add_filter( 'category_link', 'pl_remove_category' );
add_filter( 'tag_link', 'pl_remove_tag' );
add_filter( 'generate_rewrite_rules', 'pl_create_rewrite_rule' );
add_filter( 'query_vars', 'pl_query_vars', 10, 1 );
add_filter( 'author_link', 'pl_author_link' );
add_filter( 'get_comment_link', 'pl_update_comment_link' );

register_activation_hook( __FILE__, 'permalinks_activate' );
register_deactivation_hook( __FILE__, 'permalinks_deactivate' );
function pl_author_link($link) {
	
	preg_match('".*author/([a-zA-Z0-9\-]*)/?"si', $link, $matches);
	if(isset($matches[1]))
	{
		$author = get_user_by('slug', $matches[1]);
		$link = str_ireplace("story/author", "author/".$author->ID, $link);
	}

	return $link;
}

function pl_update_comment_link( $link )
{
   $link = preg_replace('".comment-page-[0-9]+"i', '', $link);
   return $link;
}

function pl_sanitize_post_link($permalink, $post, $leavename)
{
	if( $leavename || in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) return $permalink;

	return preg_replace( "/[^a-zA-Z0-9_\-\.\+\s\/\:]/", '', urldecode( $permalink ) );
}

function pl_change_page_link($permalink, $page)
{
	$tmp = explode("/", $permalink);
	$last_char = substr($permalink, -1);
	$array_len = count($tmp);
	
	$index = ($last_char == "/") ? ($array_len - 2) : ($array_len - 1); 
	
	$tmp[$index] = "special/" . $tmp[$array_len-2];
	$permalink = implode("/", $tmp);
	return $permalink;
}

function pl_remove_category($link)
{
	return str_replace("/story/category/", "/", $link);
}

function pl_remove_tag($link)
{
	return str_replace("/story/tag/", "/", $link);
}

function permalinks_activate() {
    pl_flush_rewrite_rules();
}

function permalinks_deactivate() {
    pl_flush_rewrite_rules();
}

function pl_flush_rewrite_rules()
{
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function pl_query_vars($vars)
{	
	$vars[] = 'post_id';
	$vars[] = 'city';
	$vars[] = 'match_id';
	$vars[] = 'email';
	$vars[] = 'page';
	$vars[] = 'term_slug';
	$vars[] = 'term_parent';
	return $vars;
}

function pl_create_rewrite_rule($wp_rewrite)
{
	$new_rules = array();

	// to handle feeds
	$new_rules['feed/(feed|rdf|rss|rss2|atom)/?'] = 'index.php?feed=$matches[1]'; // To handle feeds
	$new_rules['feed/?'] = 'index.php?feed=rss2'; // To handle feeds
	$new_rules['([a-zA-Z0-9\-]*)/feed/?'] = 'index.php?feed=rss2&term_slug=$matches[1]'; // To handle category feeds
	$new_rules['([a-zA-Z0-9\-]*)/([a-zA-Z0-9\-]*)/feed/?'] = 'index.php?feed=rss2&term_parent=$matches[1]&term_slug=$matches[2]'; // To handle sub-category feeds
	$new_rules['author/([0-9]*)/([a-zA-Z0-9\-]*)/feed'] = 'index.php?author=$matches[1]&feed=rss2'; // To handle author feeds
    $new_rules['story/([0-9]*)/([a-zA-Z0-9\-]*)/feed/?'] = 'index.php?feed=rss2&p=$matches[1]'; // To handle single story
   
	// to handle authors
	$new_rules['author/([0-9]*)/([a-zA-Z0-9\-]*)/page/([0-9]*)/?'] = 'index.php?author=$matches[1]&paged=$matches[3]';
	$new_rules['author/([0-9]*)/([a-zA-Z0-9\-]*)/?'] = 'index.php?author=$matches[1]';
	//$new_rules['page/?([0-9]{1,2})/?$']  = 'index.php?&paged=$matches[1]'; // To handle page numbers
	
	// to handle single stories
	$new_rules['story/([0-9]*)/(.*)/comment-page-([0-9]*)/?'] = 'index.php?p=$matches[1]&cpage=$matches[3]'; // To handle single story with comments + pagination
	$new_rules['story/([0-9]*)/([a-zA-Z0-9\-]*)/?'] = 'index.php?p=$matches[1]'; // To handle single story

	// email
	$new_rules['email/([0-9]*)/?'] = 'index.php?term_slug=email&email=$matches[1]';

	// Weather treated specially
	$new_rules['weather/([a-zA-Z0-9\-]*)/?'] = 'index.php?term_slug=weather&city=$matches[1]';

	// Cricket treated specially
	$new_rules['cricket/([a-zA-Z0-9\-]*)/?'] = 'index.php?term_slug=cricket&match_id=$matches[1]';

	// multimedia posts treated specially
	$new_rules['multimedia/([a-zA-Z]*)/page/([0-9]*)/?'] = 'index.php?term_slug=$matches[1]&term_parent=multimedia&page=$matches[2]';
	$new_rules['multimedia/videos/([0-9]*)/?'] = 'index.php?attachment_id=$matches[1]&term_slug=$matches[1]&term_parent=multimedia';
	$new_rules['multimedia/slideshows/([0-9]*)/?'] = 'index.php?p=$matches[1]&post_type=slideshow&term_parent=multimedia';
	//?p=1085283&post_type=slideshow

	// Search treated specially
	$new_rules['search/([a-zA-Z0-9\-]*)/?'] = 'index.php?s=$matches[1]';

	// to handle categories and subcategories
	//$new_rules['([a-zA-Z0-9\-]*)/([a-zA-Z0-9\-]*)/?'] = 'index.php?term_slug=$matches[2]&term_parent=$matches[1]'; // To handle sub categories
	//$new_rules['([a-zA-Z0-9\-]*)/?'] = 'index.php?term_slug=$matches[1]'; // To handle main categories

	$wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}
?>