<?php
/*
Plugin Name: Related Posts
Version: 2.4.1
Plugin URI: http://wordpress.org/extend/plugins/related-posts/
Description: Quickly increase your readers' engagement with your posts by adding Related Posts in the footer of your content.
Author: Andraz
*/

define('WP_RP_VERSION', '2.4.1');

include_once(dirname(__FILE__) . '/config.php');
include_once(dirname(__FILE__) . '/lib/stemmer.php');

include_once(dirname(__FILE__) . '/admin_notices.php');
include_once(dirname(__FILE__) . '/notifications.php');
include_once(dirname(__FILE__) . '/widget.php');
include_once(dirname(__FILE__) . '/thumbnailer.php');
include_once(dirname(__FILE__) . '/settings.php');
include_once(dirname(__FILE__) . '/recommendations.php');
include_once(dirname(__FILE__) . '/dashboard_widget.php');
include_once(dirname(__FILE__) . '/compatibility.php');

register_activation_hook(__FILE__, 'wp_rp_activate_hook');
register_deactivation_hook(__FILE__, 'wp_rp_deactivate_hook');

add_action('wp_head', 'wp_rp_head_resources');
add_action('wp_before_admin_bar_render', 'wp_rp_extend_adminbar');

function wp_rp_extend_adminbar() {
	global $wp_admin_bar;

	if(!is_super_admin() || !is_admin_bar_showing())
		return;

	$wp_admin_bar->add_menu(array(
		'id' => 'wp_rp_adminbar_menu',
		'title' => __('Related Posts', 'wp_related_posts'),
		'href' => admin_url('admin.php?page=wordpress-related-posts&ref=adminbar')
	));
}

global $wp_rp_output;
$wp_rp_output = array();
function wp_rp_add_related_posts_hook($content) {
	global $wp_rp_output, $post;
	$options = wp_rp_get_options();

	if ($post->post_type === 'post' && (($options["on_single_post"] && is_single()) || (is_feed() && $options["on_rss"]))) {
		if (!isset($wp_rp_output[$post->ID])) {
			$wp_rp_output[$post->ID] = wp_rp_get_related_posts();
		}
		$content = str_replace('%RELATEDPOSTS%', '', $content); // used for gp
		$content = $content . $wp_rp_output[$post->ID];
	}

	return $content;
}
add_filter('the_content', 'wp_rp_add_related_posts_hook', 101);

global $wp_rp_session_id, $wp_rp_test_group;
$wp_rp_session_id = false; $wp_rp_test_group = 0;
function wp_rp_get_post_url($post_id) {
	global $wp_rp_test_group;

	$post_url = get_permalink($post_id);

	$options = wp_rp_get_options();
	if (!$options['ctr_dashboard_enabled'] || !wp_is_mobile()) {
		return $post_url;
	}

	if (strpos($post_url, '?') === false) {
		$post_url .= '?wprptg=' . $wp_rp_test_group;
	} else {
		$post_url .= '&wprptg=' . $wp_rp_test_group;
	}

	return $post_url;
}
function wp_rp_init_test() {
	global $wp_rp_session_id, $wp_rp_test_group, $post;

	if ($wp_rp_session_id) {
		return;
	}

	$options = wp_rp_get_options();
	if (!$options['ctr_dashboard_enabled'] || !wp_is_mobile()) {
		return;
	}

	$wp_rp_session_id = isset($_COOKIE['wprpi']) ? $_COOKIE['wprpi'] : false;
	if (!$wp_rp_session_id) {
		$wp_rp_session_id = rand();
	}
	setcookie('wprpi', $wp_rp_session_id, time() + 60 * 30);

	$wp_rp_test_group = isset($_GET['wprptg']) ? intval($_GET['wprptg']) : false;
	if ($wp_rp_test_group !== false) {
		return;
	}

	$wp_rp_test_group = abs(crc32($wp_rp_session_id) % 2);

	if ($post && $post->post_type === 'post' && (($options["on_single_post"] && is_single()))) {
		wp_redirect(wp_rp_get_post_url($post->ID), 301);
		exit;
	}
}
add_action('template_redirect', 'wp_rp_init_test');

function wp_rp_ajax_load_articles_callback() {
	global $post, $wp_rp_test_group;

	wp_rp_init_test();

	$getdata = stripslashes_deep($_GET);
	if (!isset($getdata['post_id'])) {
		die('error');
	}

	$post = get_post($getdata['post_id']);
	if (!$post) {
		die('error');
	}

	$from = isset($getdata['from']) ? intval($getdata['from']) : 0;
	$count = isset($getdata['count']) ? intval($getdata['count']) : 50;

	$limit = $count + $from;

	$related_posts = array();

	wp_rp_append_posts($related_posts, 'wp_rp_fetch_related_posts_v2', $limit);
	wp_rp_append_posts($related_posts, 'wp_rp_fetch_related_posts', $limit);
	wp_rp_append_posts($related_posts, 'wp_rp_fetch_random_posts', $limit);

	if(function_exists('qtrans_postsFilter')) {
		$related_posts = qtrans_postsFilter($related_posts);
	}

	$response_list = array();

	foreach (array_slice($related_posts, $from) as $related_post) {
		array_push($response_list, array(
			'id' => $related_post->ID,
			'url' => wp_rp_get_post_url($related_post->ID),
			'title' => $related_post->post_title,
			'img' => wp_rp_get_post_thumbnail_img($related_post)
		));
	}

	header_remove();
	header('Content-Type: text/javascript');

	die(json_encode($response_list));
}
add_action('wp_ajax_wp_rp_load_articles', 'wp_rp_ajax_load_articles_callback');
add_action('wp_ajax_nopriv_wp_rp_load_articles', 'wp_rp_ajax_load_articles_callback');

function wp_rp_append_posts(&$related_posts, $fetch_function_name, $limit) {
	$options = wp_rp_get_options();

	$len = sizeof($related_posts);
	$num_missing_posts = $limit - $len;
	if ($num_missing_posts > 0) {
		$exclude_ids = array_map(create_function('$p', 'return $p->ID;'), $related_posts);

		$posts = call_user_func($fetch_function_name, $num_missing_posts, $exclude_ids);
		if ($posts) {
			$related_posts = array_merge($related_posts, $posts);
		}
	}
}

function wp_rp_fetch_posts_and_title() {
	$options = wp_rp_get_options();

	$limit = $options['max_related_posts'];
	$title = $options["related_posts_title"];

	$related_posts = array();

	wp_rp_append_posts($related_posts, 'wp_rp_fetch_related_posts_v2', $limit);
	wp_rp_append_posts($related_posts, 'wp_rp_fetch_related_posts', $limit);
	wp_rp_append_posts($related_posts, 'wp_rp_fetch_random_posts', $limit);

	if(function_exists('qtrans_postsFilter')) {
		$related_posts = qtrans_postsFilter($related_posts);
	}

	return array(
		"posts" => $related_posts,
		"title" => $title
	);
}

function wp_rp_generate_related_posts_list_items($related_posts) {
	$options = wp_rp_get_options();
	$output = "";
	$i = 0;

	$statistics_enabled = $options['ctr_dashboard_enabled'];

	foreach ($related_posts as $related_post ) {
		$data_attrs = '';
		if ($statistics_enabled) {
			$data_attrs .= 'data-position="' . $i++ . '" data-poid="in-' . $related_post->ID . '" ';
		}

		$output .= '<li ' . $data_attrs . '>';

		$img = wp_rp_get_post_thumbnail_img($related_post);
		if ($img) {
			$output .=  '<a href="' . wp_rp_get_post_url($related_post->ID) . '" class="wp_rp_thumbnail">' . $img . '</a>';
		}

		if (!$options["display_thumbnail"] || ($options["display_thumbnail"] && ($options["thumbnail_display_title"] || !$img))) {
			if ($options["display_publish_date"]){
				$dateformat = get_option('date_format');
				$output .= mysql2date($dateformat, $related_post->post_date) . " -- ";
			}

			$output .= '<a href="' . wp_rp_get_post_url($related_post->ID) . '" class="wp_rp_title">' . wptexturize($related_post->post_title) . '</a>';

			if ($options["display_comment_count"]){
				$output .=  " (" . $related_post->comment_count . ")";
			}

			if ($options["display_excerpt"]){
				$excerpt_max_length = $options["excerpt_max_length"];
				if($related_post->post_excerpt){
					$output .= '<br /><small>' . (mb_substr(strip_shortcodes(strip_tags($related_post->post_excerpt)), 0, $excerpt_max_length)) . '...</small>';
				} else {
					$output .= '<br /><small>' . (mb_substr(strip_shortcodes(strip_tags($related_post->post_content)), 0, $excerpt_max_length)) . '...</small>';
				}
			}
		}
		$output .=  '</li>';
	}

	return $output;
}

function wp_rp_should_exclude() {
	global $wpdb, $post;

	if (!$post || !$post->ID) {
		return true;
	}

	$options = wp_rp_get_options();

	if(!$options['exclude_categories']) { return false; }

	$q = 'SELECT COUNT(tt.term_id) FROM '. $wpdb->term_taxonomy.' tt, ' . $wpdb->term_relationships.' tr WHERE tt.taxonomy = \'category\' AND tt.term_taxonomy_id = tr.term_taxonomy_id AND tr.object_id = '. $post->ID . ' AND tt.term_id IN (' . $options['exclude_categories'] . ')';

	$result = $wpdb->get_col($q);

	$count = (int) $result[0];

	return $count > 0;
}

function wp_rp_ajax_blogger_network_blacklist_callback() {
	if (!current_user_can('delete_users')) {
		die();
	}

	$sourcefeed = (int) $_GET['sourcefeed'];

	$meta = wp_rp_get_meta();

	$blog_id = $meta['blog_id'];
	$auth_key = $meta['auth_key'];
	$req_options = array(
		'timeout' => 5
	);
	$url = WP_RP_CTR_DASHBOARD_URL . "blacklist/?blog_id=$blog_id&auth_key=$auth_key&sfid=$sourcefeed";
	$response = wp_remote_get($url, $req_options);

	if (wp_remote_retrieve_response_code($response) == 200) {
		$body = wp_remote_retrieve_body($response);
		if ($body) {
			$doc = json_decode($body);
			if ($doc && $doc->status === 'ok') {
				header_remove();
				header('Content-Type: text/javascript');
				echo "if(window['_wp_rp_blacklist_callback$sourcefeed']) window._wp_rp_blacklist_callback$sourcefeed();";
			}
		}
	}
	die();
}

add_action('wp_ajax_rp_blogger_network_blacklist', 'wp_rp_ajax_blogger_network_blacklist_callback');

function wp_rp_head_resources() {
	global $post, $wpdb, $wp_rp_test_group, $wp_rp_session_id;

	if (wp_rp_should_exclude()) {
		return;
	}

	$meta = wp_rp_get_meta();
	$options = wp_rp_get_options();
	$statistics_enabled = false;
	$remote_recommendations = false;
	$output = '';

	if (is_single()) {
		$statistics_enabled = $options['ctr_dashboard_enabled'] && $meta['blog_id'] && $meta['auth_key'];
		$remote_recommendations = $meta['remote_recommendations'] && $statistics_enabled;
	}

	if ($statistics_enabled) {
		$tags = $wpdb->get_col("SELECT label FROM " . $wpdb->prefix . "wp_rp_tags WHERE post_id=$post->ID ORDER BY weight desc;", 0);
		if (!empty($tags)) {
			$post_tags = '[' . implode(', ', array_map(create_function('$v', 'return "\'" . urlencode(substr($v, strpos($v, \'_\') + 1)) . "\'";'), $tags)) . ']';
		} else {
			$post_tags = '[]';
		}

		$output .= "<script type=\"text/javascript\">\n" .
			"\twindow._wp_rp_blog_id = '" . esc_js($meta['blog_id']) . "';\n" .
			"\twindow._wp_rp_ajax_img_src_url = '" . esc_js(WP_RP_CTR_REPORT_URL) . "';\n" .
			"\twindow._wp_rp_post_id = '" . esc_js($post->ID) . "';\n" .
			"\twindow._wp_rp_thumbnails = " . ($options['display_thumbnail'] ? 'true' : 'false') . ";\n" .
			"\twindow._wp_rp_post_title = '" . urlencode($post->post_title) . "';\n" .
			"\twindow._wp_rp_post_tags = {$post_tags};\n" .
			"\twindow._wp_rp_static_base_url = '" . esc_js(WP_RP_STATIC_BASE_URL) . "';\n" .
			"\twindow._wp_rp_promoted_content = " . ($options['promoted_content_enabled'] ? 'true' : 'false') . ";\n" .
			"\twindow._wp_rp_plugin_version = '" . WP_RP_VERSION . "';\n" .
			"\twindow._wp_rp_traffic_exchange = " . ($options['traffic_exchange_enabled'] ? 'true' : 'false') . ";\n" .
			(current_user_can('delete_users') ? "\twindow._wp_rp_admin_ajax_url = '" . admin_url('admin-ajax.php') . "';\n" : '') .
			"\twindow._wp_rp_num_rel_posts = '" . $options['max_related_posts'] . "';\n" .
			(wp_is_mobile() ?
				"\twindow._wp_rp_wp_ajax_url = \"" . admin_url('admin-ajax.php') . "\";\n" .
				"\twindow._wp_rp_test_group = " . $wp_rp_test_group . ";\n" .
				"\twindow._wp_rp_sid = \"" . $wp_rp_session_id . "\";\n"
			: '') .
			"</script>\n";
	}

	if ($remote_recommendations) {
		$output .= '<script type="text/javascript" src="' . WP_RP_STATIC_BASE_URL . WP_RP_STATIC_RECOMMENDATIONS_JS_FILE . '?version=' . WP_RP_VERSION . '"></script>' . "\n";
		$output .= '<link rel="stylesheet" href="' . WP_RP_STATIC_BASE_URL . WP_RP_STATIC_RECOMMENDATIONS_CSS_FILE . '?version=' . WP_RP_VERSION . '" />' . "\n";
	}

	if($statistics_enabled) {
		$output .= '<script type="text/javascript" src="' . WP_RP_STATIC_BASE_URL . WP_RP_STATIC_CTR_PAGEVIEW_FILE . '?version=' . WP_RP_VERSION . '" async></script>' . "\n";
	}

	if ($options['enable_themes']) {
		if ($options["display_thumbnail"]) {
			$theme_url = WP_RP_STATIC_BASE_URL . WP_RP_STATIC_THEMES_THUMBS_PATH;
		} else {
			$theme_url = WP_RP_STATIC_BASE_URL . WP_RP_STATIC_THEMES_PATH;
		}

		$output .= '<link rel="stylesheet" href="' . $theme_url . $options['theme_name'] . '?version=' . WP_RP_VERSION . '" />' . "\n";
		if ($options['custom_theme_enabled']) {
			$output .= '<style type="text/css">' . "\n" . $options['theme_custom_css'] . "</style>\n";
		}
	}

	if (wp_is_mobile()) {
		wp_enqueue_script('wp_rp_infiniterecs', WP_RP_STATIC_BASE_URL . WP_RP_STATIC_INFINITE_RECS_JS, array('jquery'));
	}

	echo $output;
}

function wp_rp_get_related_posts($before_title = '', $after_title = '') {
	if (wp_rp_should_exclude()) {
		return;
	}

	$options = wp_rp_get_options();
	$meta = wp_rp_get_meta();

	$statistics_enabled = $options['ctr_dashboard_enabled'] && $meta['blog_id'] && $meta['auth_key'];
	$remote_recommendations = is_single() && $meta['remote_recommendations'] && $statistics_enabled;

	$output = "";
	$promotional_link = '';

	$posts_and_title = wp_rp_fetch_posts_and_title();

	$related_posts = $posts_and_title['posts'];
	$title = $posts_and_title['title'];

	if (!$related_posts) {
		return;
	}

	$css_classes = 'related_post wp_rp';
	if ($options['enable_themes']) {
		$css_classes .= ' ' . str_replace(array('.css', '-'), array('', '_'), esc_attr('wp_rp_' . $options['theme_name']));
	}

	$output = wp_rp_generate_related_posts_list_items($related_posts);
	$output = '<ul class="' . $css_classes . '" style="visibility: ' . ($remote_recommendations ? 'hidden' : 'visible') . '">' . $output . '</ul>';
	if($remote_recommendations) {
		$output = $output . '<script type="text/javascript">window._wp_rp_callback_widget_exists && window._wp_rp_callback_widget_exists();</script>';
	}

	if ($title != '') {
		if ($before_title) {
			$output = $before_title . $title . $after_title . $output;
		} else {
			$title_tag = $options["related_posts_title_tag"];
			$output =  '<' . $title_tag . ' class="related_post_title">' . $title . $promotional_link . '</' . $title_tag . '>' . $output;
		}
	}

	return "\n" . $output . "\n";
}