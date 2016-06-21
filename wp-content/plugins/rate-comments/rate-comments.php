<?php
/*
	Plugin Name: Express Rate Comments
	Plugin URI:  
	Description: Enables users to like comments.
	Version: 0.1
	Author: Express Media
	Author URI: http://express.com.pk
	Plugin URI: http://tribune.com.pk
*/

define('RC_PLUGIN_DIR', trailingslashit(WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__))));
define('RC_PLUGIN_URL', trailingslashit(WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__))));

register_activation_hook(__FILE__,array('Rate_Comments','install'));

$rate_comments = new Rate_Comments();
$rate_comments->init();

Class Rate_Comments
{
	private $_db;
	private static $_RC_table;
	private $update_likes_method_ajax = 'CR_update_likes';
	private $get_post_likes_method_ajax = 'CR_get_post_likes';
	
	function __construct()
	{
		global $wpdb;
		
		$this->_db = $wpdb;
		self::$_RC_table = $this->_db->prefix . 'comments_likes';
	}

	public function init()
	{
		$this->_hook_actions();
		$this->_hook_filters();
	}
	
	private function _hook_actions()
	{
		add_action( 'wp_print_scripts', array($this,'enqueue_scripts') );
		add_action( 'wp_print_styles', array($this,'enqueue_styles') );
		add_action( 'wp_ajax_nopriv_commentsvote_ajaxhandler', 'commentsvote_ajaxhandler' );
		add_action( 'wp_ajax_commentsvote_ajaxhandler', 'commentsvote_ajaxhandler' );
	}

	private function _hook_filters()
	{
		add_filter( "xmlrpc_methods",  array( $this, "init_xml_rpc_methods" ) );
		add_filter('comment_text', array( $this, 'render_comments_like'), 10, 2 );
		add_filter('comment_text', commentsvote_comment_text);
	}
	
	function enqueue_scripts()
	{
		global $post;
		if( is_single() && $post->comment_count > 0 )
		{
			wp_enqueue_script('xmlrpc-util',RC_PLUGIN_URL.'js/xmlrpc.js',array(),false,true);
			wp_enqueue_script('xml-json-rpc-helper',RC_PLUGIN_URL.'js/xml_json_rpc_helper.js',array('xmlrpc-util'),false,true);
			wp_enqueue_script('jquery_cookie',RC_PLUGIN_URL.'js/jquery.cookie.js',array('jquery'),'0.1',true);
			wp_enqueue_script('json2',false,array(),false,true);
			wp_localize_script('comments_like', 'RC_config', array(  'xml_rpc_url' => WP_SITEURL.'/xmlrpc.php', 
																						'loading_url' => RC_PLUGIN_URL.'/loading.gif',
																 						'update_likes_method' => $this->update_likes_method_ajax,
																						'get_post_likes_method' => $this->get_post_likes_method_ajax,
																						'post_id' => $post->ID ) );
			wp_enqueue_script('like_post', RC_PLUGIN_URL.'js/post-like.js', array('jquery'), '1.0', true );
			wp_enqueue_script('comments_like',RC_PLUGIN_URL.'js/comments_like.js',array('jquery','xmlrpc-util','xml-json-rpc-helper','jquery_cookie','json2'),'0.2',true);
			wp_enqueue_script('votecomment', RC_PLUGIN_URL.'js/commentsvote.js', array('jquery'));
    		wp_localize_script( 'votecomment', 'votecommentajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			
		}
	}
	
	function enqueue_styles()
	{
		global $post;
		if( is_single() &&  $post->comment_count > 0 )
		{
			wp_enqueue_style('comments_like',RC_PLUGIN_URL.'rate-comments.css',array(),'0.2','screen');
		}
	}
	
	function init_xml_rpc_methods($wp_rpc_methods)
	{
		$wp_rpc_methods[$this->update_likes_method_ajax] = array( $this, 'update_likes' );
		$wp_rpc_methods[$this->get_post_likes_method_ajax] = array( $this, 'get_post_likes' );
		return $wp_rpc_methods;
	}
	
	public function install()
	{
		global $wpdb;
		self::$_RC_table = $wpdb->prefix . 'comments_likes';
		
		$create_table_query = 'CREATE TABLE IF NOT EXISTS `'.self::$_RC_table.'` (
									  `post_id` bigint(20) unsigned NOT NULL,
									  `comment_id` bigint(20) unsigned NOT NULL,
									  `likes` int(11) unsigned NOT NULL,
									  `remote_ips` text NOT NULL,
									  PRIMARY KEY (`post_id`,`comment_id`,`likes`)
									) ENGINE=MyISAM
									';

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $create_table_query );
	}

	public function render_comments_like( $comment_text, $comment )
	{
		if( $comment->comment_approved )
			//$comment_text .= '<span id="like-'.$comment->comment_ID.'" class="comments-like clearfix"><a class="disabled" href="#" title="Recommend This Comment">Recommend</a></span>';

		return $comment_text;
	}
	
	public function get_post_likes($post_id)
	{
		$response = array();
		$post_id = (int) $post_id;
		if($post_id)
		{
			$query = 'SELECT comment_id, likes FROM '.self::$_RC_table .' WHERE post_id = '.$post_id.' GROUP BY comment_id';
			$result = $this->_db->get_results($query,ARRAY_A);
			if($result && !empty($result))
			{
				$response = $result;
			}
		}
		
		// clean the output buffer
		while(@ob_end_clean());
		header("Content-Type: application/json");
		echo json_encode($response);
		exit;
	}
	
	public function update_likes( $params )
	{
		$response   = array('status' => 'failed');
		$remote_ip  = $_SERVER["REMOTE_ADDR"];
		$post_id    = (int) $params['post_id'];
		$comment_id = (int) $params['comment_id'];

		if( $comment_id > 0 && $post_id > 0 )
		{
			$response['comment_id'] = $comment_id;
			$comment_likes = $this->_db->get_row('SELECT * FROM '.self::$_RC_table .' WHERE comment_id = '.$comment_id.'
													AND post_id = '.$post_id);

			if ( !$comment_likes ) {
				$insert_query = 'INSERT INTO '.self::$_RC_table .' (post_id, comment_id, likes, remote_ips) VALUES('.$post_id.', '.$comment_id.', 1, \''.maybe_serialize( array( $remote_ip ) ).'\')';
				$rows_updated = $this->_db->query($insert_query);
			}
			else {
//				$comment_like_ips = (array)maybe_unserialize( $comment_likes->remote_ips );
//				if ( !in_array($remote_ip, $comment_like_ips) ) {
					$comment_like_ips[] = $remote_ip;
					
					$update_query = 'UPDATE '.self::$_RC_table .' SET likes = likes+1, remote_ips =\''.maybe_serialize( $comment_like_ips ).'\' WHERE comment_id = '.$comment_id.' AND post_id= '.$post_id;
					$rows_updated = $this->_db->query($update_query);
//				}
//				else
//				{
//					/* IP addres has already up-voted comment. handle here */
//				}
			}
			// success is always sent even if this is a duplicate like
			if( $rows_updated !== false ) $response['status'] = 'success';
		}
		// clean the output buffer
		while(@ob_end_clean());
		header("Content-Type: application/json");
		echo json_encode($response);
		exit;
	}
}

function commentsvote_showlink() {
    $link = "";
    $nonce = wp_create_nonce("commentsvote_nonce");
    $current_CommentID =  get_comment_ID();
    $p_id = get_the_ID();
    $votes = get_comment_meta($current_CommentID, '_commentsvote', true) != '' ? get_comment_meta($current_CommentID, '_commentsvote', true) : '0';
    $arguments = $p_id.",".$current_CommentID.",'".$nonce."'";
    $link = '<span class="number">'.$votes.'</span> <a onclick="commentsvote_add('.$arguments.');">'.'Recommend'.'</a>';
    $completelink = '<div id="commentsvote-'.$current_CommentID.'">';
    $completelink .= '<span class="comments-like">'.$link.'</span>';
    $completelink .= '</div>';
    return $completelink;
}

function commentsvote_comment_text($content) {
    return $content.commentsvote_showlink();
}


function commentsvote_ajaxhandler() {
    if ( !wp_verify_nonce( $_POST['nonce'], "commentsvote_nonce")) {
        exit("Something Wrong");
    }
    $results = '';
        $ip = $_SERVER['REMOTE_ADDR'];
    global $wpdb;
 	$commentid = $_POST['commentid'];
 	$p_id = $_POST['p_id'];
    $votecount = get_comment_meta($commentid, '_commentsvote', true) != '' ? get_comment_meta($commentid, '_commentsvote', true) : '0';
    $votecountNew = $votecount + 1;
    update_comment_meta($commentid, '_commentsvote', $votecountNew);
	if ( !$votecount ) {
				$insert_query = 'INSERT INTO `wp_comments_likes`(`post_id`, `comment_id`, `likes`, `remote_ips`) VALUES('.$p_id.', '.$commentid.', 1, \''.maybe_serialize( array( $ip ) ).'\')';
				$rows_updated = $wpdb->query($insert_query);
			}
			else {
						
				$update_query = 'UPDATE `wp_comments_likes` SET `likes`='.$votecountNew.',`remote_ips`=\''.maybe_serialize( $ip ).'\' WHERE comment_id = '.$commentid.' AND post_id='.$p_id ;
				$rows_updated = $wpdb->query($update_query);
			}
	    $results.='<div class="votescore comments-like"  ><span class="number">'.$votecountNew.'</span><a class="disabled number">'.'Recommend'.'</a></div>'; 

				
				

    //Return the String
    die($results);
}
// creating Ajax call for WordPress


