<?php
/**
* Plugin Name: Express Mobile ShareBar
* Plugin URI: http://www.express.pk
* Description: Displays a floating share bar for Facebook, Twitter and WhatsApp at bottom via mobile.
* Version: 1.0
* Author: Express Media
* Author URI: http://express.pk
*/
/* define path */
define('kakinetwork_url',plugin_dir_url(__FILE__ ));
define('kakinetwork_path',plugin_dir_path(__FILE__ ));

/* ects */
require_once kakinetwork_path . "kn_ects.php";
register_activation_hook(__FILE__,'install_kn_mobile_sharebar');

/* wtf */
function install_kn_mobile_sharebar() {
	$kn_plugin="kn_mobile_sharebar";
	add_option( $kn_plugin."_where", "bottom" );
	add_option( $kn_plugin."_facebook", "[kn-post-url]" );
	add_option( $kn_plugin."_twitter", "Let's read [kn-post-title] by click this [kn-post-url] !! :)" );
	add_option( $kn_plugin."_whatsapp", "Hello my friend! :) I have something nice to share. It's about [kn-post-title]!! Read now by click this link [kn-post-url]" );
	add_option( $kn_plugin."_visibility_homepage", "off" );
	add_option( $kn_plugin."_visibility_post", "on" );
	add_option( $kn_plugin."_visibility_page", "off" );
	add_option( $kn_plugin."_height", "50" );
	add_option( $kn_plugin."_small_desktop", "off" );
}

/* in action */
if ( !is_home() && !is_category() ) {
	add_action( 'wp_enqueue_scripts', 'add_css' );
	add_action( 'wp_enqueue_scripts', 'msb_add_js' );
}


function msb_add_js() {
    if ( !is_home() && !is_category() ) {
	wp_enqueue_script(
		'mobile_share_bar',
		plugins_url( '/js/mobile_sharebar.js' , __FILE__ ),
		array( 'jquery' )
	);
    }
}

function add_css() {
    if ( !is_home() && !is_category() ) {
	wp_register_style( 'mobile_share', kakinetwork_url . 'css/mobile_sharebar.css');
	wp_enqueue_style( 'mobile_share' );
    }
}


/* more specific isn't? */
if(get_option( 'kn_mobile_small_desktop' )=="on") {
	if(get_option( 'kn_mobile_sharebar_where' )=="top") {
		add_action('wp_head', 'mobile_sharebar_add');
	}
	if(get_option( 'kn_mobile_sharebar_where' )=="bottom") {
		add_action('wp_footer', 'mobile_sharebar_add');
	}
}
if(get_option( 'kn_mobile_small_desktop' )!="on") {
	if ( wp_is_mobile() ) {
		if(get_option( 'kn_mobile_sharebar_where' )=="top") {
			add_action('wp_head', 'mobile_sharebar_add');
		}
		if(get_option( 'kn_mobile_sharebar_where' )=="bottom") {
			add_action('wp_footer', 'mobile_sharebar_add');
		}
	}
}

function mobile_sharebar_add(){
	
	if(get_option( 'kn_mobile_sharebar_visibility_homepage' )=="on") {
		$visibility_homepage = is_front_page();
	}
	if(get_option( 'kn_mobile_sharebar_visibility_post' )=="on") {
		$visibility_single = is_single();
	}
	if(get_option( 'kn_mobile_sharebar_visibility_page' )=="on") {
		$visibility_page = is_page();
	}
	
	if ( $visibility_homepage || $visibility_single || $visibility_page ) {

		$replacements = array(
			'#' => '',
			'&' => 'and',
			'?' => '',
			';' => '',
			'038' => '',
			'and8220' => '',
			'and8221' => '',
			'and8211' => '',
			'and8216' => '',
			'and8217' => ''
		);
		
		$title_text = ($visibility_single) ? get_the_title(): "";

		$facebook_text = $title_text;
		$twitter_text = $title_text;
		$twitter_text_fix = str_replace(array_keys($replacements), $replacements, $twitter_text);		
		$whatsapp_text = $title_text;
		$whatsapp_text_fix = str_replace(array_keys($replacements), $replacements, $whatsapp_text);
			
		echo '
		<div class="mobile-share-bar" style="display:none;">
			<div id="mobile-share-box'.get_option( 'kn_mobile_sharebar_where' ).'" style="height: '.get_option( 'kn_mobile_sharebar_height' ).'px;">
				<ul id="horizontal-list" style="height: '.get_option( 'kn_mobile_sharebar_height' ).'px;">	
					<li class="knfb" style="height: '.get_option( 'kn_mobile_sharebar_height' ).'px;"><a href="https://www.facebook.com/sharer.php?u='.get_permalink().'" target="_blank" class="hyperlink" style="height: '.get_option( 'kn_mobile_sharebar_height' ).'px;">facebook</a></li>
					<li class="kntw" style="height: '.get_option( 'kn_mobile_sharebar_height' ).'px;"><a href="https://twitter.com/intent/tweet?source=tweetbutton&amp;url='.get_permalink()."&amp;text=".$twitter_text_fix.'" target="_blank" class="hyperlink" style="height: '.get_option( 'kn_mobile_sharebar_height' ).'px;">twitter</a></li>
					<li class="knws" style="height: '.get_option( 'kn_mobile_sharebar_height' ).'px;"><a href="whatsapp://send?text='.$whatsapp_text_fix . ' ' . get_permalink().'" data-action="share/whatsapp/share" target="_blank" class="hyperlink" style="height: '.get_option( 'kn_mobile_sharebar_height' ).'px;">whatsapp</a></li>
				</ul>
			</div>
			<div id="mobile-sharebar-footer'.get_option( 'kn_mobile_sharebar_where' ).' style="height: '.get_option( 'kn_mobile_sharebar_height' ).'px;"">
				<img class="share-icon" style="height: '.get_option( 'kn_mobile_sharebar_height' ).'px;" src="' . kakinetwork_url . 'images/blank.png" border="0"/>
			</div>
		</div>
		';
	}
}
?>
