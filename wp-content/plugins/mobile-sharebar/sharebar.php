<?php
/**
 * Plugin Name: Mobile ShareBar
 * Plugin URI: http://somutech.de/2014/04/wp-plugin-mobile-sharebar/
 * Description: Displays a customizable bar with WhatsApp, Twitter, Facebook and Google+ share buttons on top or bottom of your website. Currently the WhatsApp Button only works on iPhones and Smartphones using the newest Android version. :)
 * Version: 1.2.1
 * Author: somutech.de
 * Author URI: http://somutech.de/
 * License: GPLv3
 */

//created by Maik Hebestadt on behalf of somutech.de :)

define('SHAREBAR_VERSION', '1.2.1');

add_action('admin_menu', 'mobile_sharebar_add_menu');
add_action('wp_head', 'mobile_sharebar_initialize');
add_action('wp_footer', 'mobile_sharebar_add');

add_action('load-post.php', 'mobile_sharebar_meta_box_setup');
add_action('load-post-new.php', 'mobile_sharebar_meta_box_setup');
add_action('load-page.php', 'mobile_sharebar_meta_box_setup');
add_action('load-page-new.php', 'mobile_sharebar_meta_box_setup');

add_action('admin_print_scripts', 'mobile_sharebar_admin_scripts');
add_action('admin_print_styles', 'mobile_sharebar_admin_styles');
add_action('wp_print_styles', 'mobile_sharebar_styles');
add_action('wp_print_scripts', 'mobile_sharebar_scripts');

function mobile_sharebar_add_menu() {
	add_options_page('Mobile ShareBar', 'Mobile ShareBar', 'manage_options', 'mobilesharebar', 'mobile_sharebar_main');
}

function mobile_sharebar_meta_box_setup() {
	add_action( 'add_meta_boxes', 'mobile_sharebar_add_meta_box' );
	add_action( 'save_post', 'mobile_sharebar_meta_box_save', 10, 2 );
}

function mobile_sharebar_add_meta_box() {
	add_meta_box(
		'mobile-sharebar-meta',			
		'Custom WhatsApp Share Text',
		'mobile_sharebar_meta_box',
		'post',
		'side',
		'default'
	);
	add_meta_box(
		'mobile-sharebar-meta',			
		'Custom WhatsApp Share Text',
		'mobile_sharebar_meta_box',
		'page',
		'side',
		'default'
	);
}

function mobile_sharebar_meta_box( $object, $box ) {
	if(get_locale() != 'de_DE'){
		$string[0] = '<b>Placeholder:</b> %TITLE% - Post Title, %URL% - Permalink';
	}else{
		$string[0] = '<b>Platzhalter:</b> %TITLE% - Post Titel, %URL% - Permalink';
	}

	wp_nonce_field( basename( __FILE__ ), 'mobile_sharebar_meta_box_nonce' );
	?>
	<p>
		<textarea class="widefat" type="text" name="mobile-sharebar-meta" id="mobile-sharebar-meta" ><?php echo esc_attr( stripslashes(get_post_meta( $object->ID, 'mobile_sharebar_whatsapp_text', true )) ); ?></textarea><br/>
		<small><?php echo $string[0]; ?></small>
	</p>
	<?php
}

function mobile_sharebar_meta_box_save( $post_id, $post ) {
	if ( !isset( $_POST['mobile_sharebar_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['mobile_sharebar_meta_box_nonce'], basename( __FILE__ ) ) ){
		return $post_id;
	}
	$post_type = get_post_type_object( $post->post_type );
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) ){
		return $post_id;
	}
	$new_meta_value = ( isset( $_POST['mobile-sharebar-meta'] ) ? mysql_real_escape_string($_POST['mobile-sharebar-meta'])  : '' );
	$meta_key = 'mobile_sharebar_whatsapp_text';
	$meta_value = get_post_meta( $post_id, $meta_key, true );
	if ( $new_meta_value && $meta_value == ''){
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );
	}elseif ( $new_meta_value && $new_meta_value != $meta_value ){
		update_post_meta( $post_id, $meta_key, $new_meta_value );
	}elseif ( $new_meta_value == '' && $meta_value ){
		update_post_meta( $post_id, $meta_key, '' );
		delete_post_meta( $post_id, $meta_key, $meta_value );
	}
}

function mobile_sharebar_styles(){
	if(get_option('sharebar_use_customcss') != true){
		wp_enqueue_style( 'sharebar', plugins_url('sharebar.css', __FILE__) );
	}
}

function mobile_sharebar_scripts(){
	if(get_option('sharebar_include_jquery') == true){
		wp_deregister_script('jquery');
		wp_register_script('jquery', ("//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"), false, '1.10.1', false);
		wp_enqueue_script('jquery');
	}
	wp_enqueue_script( 'sharebar_init', plugins_url('sharebar.js', __FILE__) );
}

function mobile_sharebar_admin_styles(){
	wp_enqueue_style( 'sharebar-admin', plugins_url('style.css', __FILE__) );
	if(get_option('sharebar_use_customcss') != true){
		wp_enqueue_style( 'sharebar', plugins_url('sharebar.css', __FILE__) );
	}
	wp_enqueue_style( 'yandex', "http://yandex.st/highlightjs/8.0/styles/default.min.css");
}

function mobile_sharebar_admin_scripts(){
	wp_enqueue_script( 'yandex', "http://yandex.st/highlightjs/8.0/highlight.min.js");
}

function make_bitly_url($url,$login,$appkey,$format = 'xml',$version = '2.0.1')
{
	$bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;
	$response = file_get_contents($bitly);
	if(strtolower($format) == 'json'){
		$json = @json_decode($response,true);
		return $json['results'][$url]['shortUrl'];
	}else{
		$xml = simplexml_load_string($response);
		return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
	}
}

function mobile_sharebar_initialize(){
	$force_sharebar = false;
	if ( is_front_page() ) {
		$curPage = 'frontpage';
	} elseif ( is_page()) {
		$curPage = 'page';
	} elseif ( !is_page() AND is_single() AND get_post_type() == 'post') {
		$curPage = 'post';
	} elseif (is_category()) {
		$curPage = 'category';
	} elseif (is_archive()) {
		$curPage = 'archive';
	} elseif (is_tag()) {
		$curPage = 'tag';
	} elseif ( !is_page() AND is_single() AND get_post_type() != 'post') {
		$allowedPostTypes = explode(',', str_replace(' ', '', get_option('sharebar_custom_post_types')));
		if(!empty($allowedPostTypes) AND in_array(get_post_type(), $allowedPostTypes)){
			$force_sharebar = true;
		}
	}
	if(get_option('sharebar_automatic') != '' AND ($force_sharebar == true OR in_array( $curPage, get_option('sharebar_automatic_pages')))){
		$shareText = get_option('sharebar_whatsapp_sharetext');
		$mobile_sharebar_whatsapp_text = get_post_meta( get_the_id(), 'mobile_sharebar_whatsapp_text', true );
		if ( !empty( $mobile_sharebar_whatsapp_text ) ){ $shareText = $mobile_sharebar_whatsapp_text; }
		//$shareText = str_replace('%URL%', get_permalink(), $shareText);
		$shareText = html_entity_decode(str_replace('%TITLE%', mb_convert_encoding(get_the_title(), "UTF-16", "HTML-ENTITIES"), $shareText));
		$shareText = str_replace("%00", "", rawurlencode(stripslashes($shareText)));
		//$shareText = rawurlencode(stripslashes($shareText));
		$additional = '';
		if(get_option('sharebar_show_whatsapp') == true){$additional .= '"whatsapp":true, ';}
		if(get_option('sharebar_show_facebook') == true){$additional .= '"facebook":true, ';}
		if(get_option('sharebar_show_twitter') == true){$additional .= '"twitter":true, ';}
		if(get_option('sharebar_show_google') == true){$additional .= '"google":true, ';}
		if(get_option('sharebar_bar_position') != 'top'){$additional .= '"position":"bottom", ';}
		if(get_option('sharebar_rtl') == true){$additional .= '"rtl":true, ';}
		if(get_option('sharebar_everywhere') == true){$additional .= '"everywhere":true, ';}
		if(get_option('sharebar_bitly_active') == true){
			$theShareURL = make_bitly_url(get_permalink(),get_option('sharebar_bitly_login'),get_option('sharebar_bitly_apikey'),'json');
		}else{
			$theShareURL = get_permalink();
		}
		echo '<script type="text/javascript">shareBar.init({'.$additional.'"text":"'.$shareText.'", "url":"'.urlencode($theShareURL).'", "share_fb":"'.get_option('sharebar_buttontext_facebook').'", "share_wa":"'.get_option('sharebar_buttontext_whatsapp').'", "share_tw":"'.get_option('sharebar_buttontext_twitter').'", "share_g":"'.get_option('sharebar_buttontext_google').'"});</script>'."\n";
if(get_option('sharebar_use_customcss') == true){ ?>
<style type="text/css">

<?php echo get_option('sharebar_customcss'); ?>

</style>
<?php }
	}
}

function mobile_sharebar_add(){
	$force_sharebar = false;
	if ( is_front_page() OR is_home() ) {
		$curPage = 'frontpage';
		if(get_option('sharebar_footer_link') != '' AND (is_front_page() OR is_home())){
			if(get_locale() != 'de_DE'){
				$string[0] = 'This Website is using <a href="http://somutech.de/wp-plugin-mobile-sharebar/" target="_blank"><b>Mobile ShareBar</b></a> to display the WhatsApp Share Button on mobile devices.';
			}else{
				$string[0] = 'Diese Webseite nutzt <a href="http://somutech.de/wp-plugin-mobile-sharebar/" target="_blank"><b>Mobile ShareBar</b></a>, um einen WhatsApp Share Button auf mobilen Geräten anzuzeigen.';
			}
			echo '<div id="mbl-sharebar-footer"><span class="mbl-sharebar-footer-span">'.$string[0].'</span></div>';
		}
	} elseif ( is_page()) {
		$curPage = 'page';
	} elseif ( !is_page() AND is_single() AND get_post_type() == 'post') {
		$curPage = 'post';
	} elseif (is_category()) {
		$curPage = 'category';
	} elseif (is_archive()) {
		$curPage = 'archive';
	} elseif (is_tag()) {
		$curPage = 'tag';
	} elseif ( !is_page() AND is_single() AND get_post_type() != 'post') {
		$allowedPostTypes = explode(',', str_replace(' ', '', get_option('sharebar_custom_post_types')));
		if(!empty($allowedPostTypes) AND in_array(get_post_type(), $allowedPostTypes)){
			$force_sharebar = true;
		}
	}
	if(get_option('sharebar_automatic') != '' AND ($force_sharebar == true OR in_array( $curPage, get_option('sharebar_automatic_pages')))){
		echo '<script type="text/javascript">shareBar.show();</script>'."\n";
	}
}

function mobile_sharebar_main() {

	if(get_locale() != 'de_DE'){
		$string[0] = 'Displays a customizable bar with WhatsApp, Twitter, Facebook and Google+ share buttons on top or bottom of your website. Currently the WhatsApp Button only works on iPhones and Smartphones using the newest Android version.';
		$string[1] = '<b>Share Text</b><br/><small>only for WhatsApp & Twitter</small>';
		$string[2] = 'Display Facebook button';
		$string[3] = 'Display Settings';
		$string[4] = 'Top of page';
		$string[5] = 'Bottom of page';
		$string[6] = 'Show at';
		$string[7] = 'Front Page';
		$string[8] = 'Pages';
		$string[9] = 'Categories';
		$string[10] = 'Archives';
		$string[11] = 'Seperate multiple Custom Post Types by comma';
		$string[12] = 'Troubleshooting and other Stuff';
		$string[13] = 'Display Mobile ShareBar automatically';
		$string[14] = 'Instruction on how to embed it manually will be shown after you uncheck this box.';
		$string[15] = 'Include jQuery';
		$string[16] = 'If the bar won\'t show up, check this box. Uncheck if necessary.';
		$string[17] = '\'Mobile ShareBar\'-Link @ Frontpage';
		$string[18] = 'It\'d be nice if you wouldn\'t uncheck this box. Thank you! :)';
		$string[19] = 'Preview';
		$string[20] = 'Do you like this plugin?';
		$string[21] = 'This awesome Plugin will help you to get more and more visitors to your website and it\'s <b>completely free</b>! We would be glad, if you like to <b>make a donation to support</b> our developers. Thank you!';
		$string[22] = 'https://www.paypalobjects.com/en_US/DE/i/btn/btn_donateCC_LG.gif';
		$string[23] = '&nbsp;%TITLE% - Title, %URL% - Permalink, %NL% - Line break';
		$string[24] = 'Posts';
		$string[25] = 'Semitic Language';
		$string[26] = 'Check, if you\'re using any semitic scripts (e.g. Arabic, Hebrew, ...)';
		$string[27] = 'Show on every mobile device';
		$string[28] = 'WhatsApp-Button will be shown on iPhones and Android Smartphones only';
		$string[29] = 'Display Twitter button';
		$string[30] = 'Get it here: <a href="https://bitly.com/a/settings/advanced" target="_blank">https://bitly.com/a/settings/advanced</a>';
		$string[31] = 'Shorten URLs';
		$string[32] = 'Display Google+ button';
		$string[33] = 'Button Options';
		$string[35] = 'Display WhatsApp Button';
		$string[36] = 'Custom CSS';
		$string[37] = 'Use custom CSS<br/><small>(This will replace the MobileSharebar CSS, <br/>but can be revoked at any time)<small>';
	}else{
		$string[0] = 'Zeigt WhatsApp, Twitter, Facebook und Google+ ShareButtons - wahlweise am oberen oder unteren Bereich der Webseite - an. Da der WhatsApp ShareButton vorerst nur auf dem iPhone und Android Smartphones mit der neusten Version funktioniert, wird dieser nur Besuchern angezeigt, die die Seite mit einem solchen Smartphone aufrufen.';
		$string[1] = '<b>Share Text</b><br/><small>nur für Twitter & WhatsApp</small>';
		$string[2] = 'Facebook Button anzeigen';
		$string[3] = 'Darstellung';
		$string[4] = 'Im oberen Bereich der Seite';
		$string[5] = 'Im unteren Bereich der Seite';
		$string[6] = 'Anzeigen auf';
		$string[7] = 'Hauptseite';
		$string[8] = 'Seiten';
		$string[9] = 'Kategorien';
		$string[10] = 'Archiven';
		$string[11] = 'Trenne mehrere Custom Post Types mit einem Komma';
		$string[12] = 'Fehlerbehebung und anderes Zeug';
		$string[13] = 'Zeige Mobile ShareBar automatisch';
		$string[14] = 'Der Code zum manuellen Einbinden erscheint, wenn du den Haken entfernst.';
		$string[15] = 'jQuery einbinden';
		$string[16] = 'Nötig, sofern die Sharebar nicht angezeigt wird.';
		$string[17] = '\'Mobile ShareBar\'-Link auf der Hauptseite';
		$string[18] = 'Es wäre nett, wenn du den Haken nicht enfernst. Danke :)';
		$string[19] = 'Vorschau';
		$string[20] = 'Gefällt dir das Plugin?';
		$string[21] = 'Dieses tolle Plugin wird dir dabei helfen immer mehr Besucher auf deine Seite zu bringen und es ist <b>komplett kostenlos</b>! Wir würden uns trotzdem freuen, wenn du unsere Entwickler <b>mit einer Spende unterstützen</b> könntest. Danke!';
		$string[22] = 'https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donateCC_LG.gif';
		$string[23] = '&nbsp;%TITLE% - Titel, %URL% - Permalink, %NL% - Zeilenumbruch';
		$string[24] = 'Beiträge';
		$string[25] = 'Semitische Sprache';
		$string[26] = 'Check, wenn du eine semitische (RTL) Sprache nutzt (z.B: Arabisch, Hebräisch, ...)';
		$string[27] = 'Auf jedem mobilen Gerät anzeigen';
		$string[28] = 'WhatsApp-Button wird nur auf iPhones und Android Smartphones angezeigt';
		$string[29] = 'Twitter Button anzeigen';
		$string[30] = 'Get it here: <a href="https://bitly.com/a/settings/advanced" target="_blank">https://bitly.com/a/settings/advanced</a>';
		$string[31] = 'URLs kürzen';
		$string[32] = 'Google+ Button anzeigen';
		$string[33] = 'Button Optionen';
		$string[34] = 'Die Texte sind nur relevant, solltest du die "eigenes CSS"-Funktion benutzen.<br/> Wir wollten die Möglichkeit der eigenen Texte nicht entfernen.';
		$string[35] = 'WhatsApp Button anzeigen';
		$string[36] = 'Eigenes CSS';
		$string[37] = 'Eigenes CSS benutzen<br/><small>(Ersetzt das MobileSharebar CSS. Kann jederzeit Rückgängig gemacht werden)</small>';
	}

	add_option('sharebar_ping', false);
	add_option('sharebar_include_jquery', true);
	add_option('sharebar_automatic', true);
	add_option('sharebar_automatic_pages', array('page','post'));
	add_option('sharebar_custom_post_types', '');
	add_option('sharebar_bar_position', 'bottom');
	add_option('sharebar_show_whatsapp', true);
	add_option('sharebar_show_facebook', true);
	add_option('sharebar_show_twitter', true);
	add_option('sharebar_show_google', true);
	add_option('sharebar_footer_link', true);
	add_option('sharebar_use_customcss', false);
	add_option('sharebar_customcss', '#mbl-sharebar{}
#mbl-sharebar.sharebar-rtl{}
#mbl-sharebar.sharebar-top{}
#mbl-sharebar.sharebar-bottom{}
#mbl-sharebar.sharebar-landscape{}
#mbl-sharebar .sharebar-button{}
#mbl-sharebar .sharebar-button.sharebar-facebook{}
#mbl-sharebar .sharebar-button.sharebar-twitter{}
#mbl-sharebar .sharebar-button.sharebar-whatsapp{}
#mbl-sharebar .sharebar-button.sharebar-google{}');
	add_option('sharebar_buttontext_whatsapp', 'Share via WhatsApp');
	add_option('sharebar_buttontext_facebook', 'Share via Facebook');
	add_option('sharebar_buttontext_twitter', 'Tweet it!');
	add_option('sharebar_buttontext_google', 'Share via Google+');
	add_option('sharebar_whatsapp_sharetext', '%TITLE%: %URL%');
	add_option('sharebar_rtl', false);
	add_option('sharebar_everywhere', false);
	add_option('sharebar_bitly_active', false);
	add_option('sharebar_bitly_login', '');
	add_option('sharebar_bitly_apikey', '');
	if(!empty($_POST)){
		update_option('sharebar_buttontext_facebook', $_POST['sharebar_buttontext_facebook']);
		update_option('sharebar_buttontext_whatsapp', $_POST['sharebar_buttontext_whatsapp']);
		update_option('sharebar_buttontext_twitter', $_POST['sharebar_buttontext_twitter']);
		update_option('sharebar_buttontext_google', $_POST['sharebar_buttontext_google']);
		update_option('sharebar_show_facebook', $_POST['sharebar_show_facebook']);
		update_option('sharebar_show_twitter', $_POST['sharebar_show_twitter']);
		update_option('sharebar_show_google', $_POST['sharebar_show_google']);
		update_option('sharebar_show_whatsapp', $_POST['sharebar_show_whatsapp']);
		update_option('sharebar_bar_position', $_POST['sharebar_bar_position']);
		update_option('sharebar_automatic_pages', $_POST['sharebar_automatic_pages']);
		update_option('sharebar_automatic', $_POST['sharebar_automatic']);
		update_option('sharebar_include_jquery', $_POST['sharebar_include_jquery']);
		update_option('sharebar_whatsapp_sharetext', $_POST['sharebar_whatsapp_sharetext']);
		update_option('sharebar_footer_link', $_POST['sharebar_footer_link']);
		update_option('sharebar_custom_post_types', $_POST['sharebar_custom_post_types']);
		update_option('sharebar_rtl', $_POST['sharebar_rtl']);
		update_option('sharebar_everywhere', $_POST['sharebar_everywhere']);
		update_option('sharebar_bitly_login', $_POST['sharebar_bitly_login']);
		update_option('sharebar_bitly_apikey', $_POST['sharebar_bitly_apikey']);
		update_option('sharebar_bitly_active', $_POST['sharebar_bitly_active']);
		update_option('sharebar_use_customcss', $_POST['sharebar_use_customcss']);
		update_option('sharebar_customcss', $_POST['sharebar_customcss']);
	}
	$ping = '';
	if(get_option('sharebar_ping') == false){
		$ping = '&ping='.get_bloginfo('url');
		update_option('sharebar_ping', true);
	}
	?>

	<div class="wrap" id="sharebar_admin">
		<a href="http://somutech.de/" target="_blank" id="logo">SOMUTECH.de</a>
		<h2>Mobile ShareBar <?php echo SHAREBAR_VERSION; ?></h2>
		<small style="max-width: 400px; color: #666666; display: block; margin-bottom: 15px;"><?php echo $string[0]; ?></small>
		<form method="POST" style="display: inline-block;">
			<table>
				<tr valign="top">
					<td colspan="2"><?php echo $string[1]; ?></td>
				</tr>

				<tr valign="top">
					<td>Text</td>
					<td>
						<textarea name="sharebar_whatsapp_sharetext" style="width: 300px; height: 50px;"><?php echo get_option('sharebar_whatsapp_sharetext'); ?></textarea><br/><small style="display: block; padding: 2px; border: solid 1px #dddddd; border-top: 0px; position: relative; top: -1px; background-color: #fff;"><?php echo $string[23]; ?></small>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2"><b><?php echo $string[33]; ?></b><br><small><?php echo $string[34]; ?></small></td>
				</tr>
				<tr valign="top">
					<td>Facebook</td>
					<td><input type="text" name="sharebar_buttontext_facebook" value="<?php echo get_option('sharebar_buttontext_facebook'); ?>" /><br/>
						<label for="showfb"><input type="checkbox" name="sharebar_show_facebook" value="1" id="showfb"<?php if(get_option('sharebar_show_facebook') == true){echo ' checked';} ?> /><small style="position: relative; top: -2px;"><?php echo $string[2]; ?></small></label>
					</td>
				</tr>
				<tr valign="top">
					<td>Twitter</td>
					<td><input type="text" name="sharebar_buttontext_twitter" value="<?php echo get_option('sharebar_buttontext_twitter'); ?>" /><br/>
						<label for="showtwitter"><input type="checkbox" name="sharebar_show_twitter" value="1" id="showtwitter"<?php if(get_option('sharebar_show_twitter') == true){echo ' checked';} ?> /><small style="position: relative; top: -2px;"><?php echo $string[29]; ?></small></label>
					</td>
				</tr>
				<tr valign="top">
					<td width="80">WhatsApp</td>
					<td><input type="text" name="sharebar_buttontext_whatsapp" value="<?php echo get_option('sharebar_buttontext_whatsapp'); ?>" /><br/>
						<label for="showwa"><input type="checkbox" name="sharebar_show_whatsapp" value="1" id="showwa"<?php if(get_option('sharebar_show_whatsapp') == true){echo ' checked';} ?> /><small style="position: relative; top: -2px;"><?php echo $string[35]; ?></small></label>
				</tr>
				<tr valign="top">
					<td>Google+</td>
					<td><input type="text" name="sharebar_buttontext_google" value="<?php echo get_option('sharebar_buttontext_google'); ?>" /><br/>
						<label for="showg"><input type="checkbox" name="sharebar_show_google" value="1" id="showg"<?php if(get_option('sharebar_show_google') == true){echo ' checked';} ?> /><small style="position: relative; top: -2px;"><?php echo $string[32]; ?></small></label>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2"><b>bit.ly Connection</b></td>
				</tr>
				<tr valign="top">
					<td>Login</td>
					<td><input type="text" name="sharebar_bitly_login" value="<?php echo get_option('sharebar_bitly_login'); ?>" /></td>
				</tr>
				<tr valign="top">
					<td>API Key</td>
					<td><input type="text" name="sharebar_bitly_apikey" value="<?php echo get_option('sharebar_bitly_apikey'); ?>" /><br/><small><?php echo $string[30]; ?></small></td>
				</tr>
				<tr valign="top">
					<td></td>
					<td><label for="bitly"><input type="checkbox" name="sharebar_bitly_active" value="1" id="bitly"<?php if(get_option('sharebar_bitly_active') == true){echo ' checked';} ?> /><?php echo $string[31]; ?></label></td>
				</tr>
				<tr valign="top">
					<td colspan="2"><b><?php echo $string[3]; ?></b></td>
				</tr>
				<tr valign="top">
					<td>Position</td>
					<td>
						<select name="sharebar_bar_position">
							<option<?php if(get_option('sharebar_bar_position') == "top"){echo ' selected';} ?> value="top"><?php echo $string[4]; ?></option>
							<option<?php if(get_option('sharebar_bar_position') == "bottom"){echo ' selected';} ?> value="bottom"><?php echo $string[5]; ?></option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<td><?php echo $string[6]; ?></td>
					<td>
						<label for="x1"><input type="checkbox" name="sharebar_automatic_pages[]" value="frontpage" id="x1"<?php if(in_array( 'frontpage', get_option('sharebar_automatic_pages'))){echo ' checked';} ?> /><?php echo $string[7]; ?></label><br/>
						<label for="x2"><input type="checkbox" name="sharebar_automatic_pages[]" value="page" id="x2"<?php if(in_array( 'page', get_option('sharebar_automatic_pages'))){echo ' checked';} ?> /><?php echo $string[8]; ?></label><br/>
						<label for="x3"><input type="checkbox" name="sharebar_automatic_pages[]" value="post" id="x3"<?php if(in_array( 'post', get_option('sharebar_automatic_pages'))){echo ' checked';} ?> /><?php echo $string[24]; ?></label><br/>
						<label for="x4"><input type="checkbox" name="sharebar_automatic_pages[]" value="category" id="x4"<?php if(in_array( 'category', get_option('sharebar_automatic_pages'))){echo ' checked';} ?> /><?php echo $string[9]; ?></label><br/>
						<label for="x5"><input type="checkbox" name="sharebar_automatic_pages[]" value="archive" id="x5"<?php if(in_array( 'archive', get_option('sharebar_automatic_pages'))){echo ' checked';} ?> /><?php echo $string[10]; ?></label><br/>
						<label for="x6"><input type="checkbox" name="sharebar_automatic_pages[]" value="tag" id="x6"<?php if(in_array( 'tag', get_option('sharebar_automatic_pages'))){echo ' checked';} ?> />Tags</label><br/>
						<label for="x7"><input type="checkbox" name="sharebar_automatic_pages[]" value="other" id="x7"<?php if(in_array( 'other', get_option('sharebar_automatic_pages'))){echo ' checked';} ?> />Custom Post Types<span class="cpt">:</span></label><br/>
						<span class="cpt"><input type="text" name="sharebar_custom_post_types" value="<?php echo get_option('sharebar_custom_post_types'); ?>" /><br/><small><?php echo $string[11]; ?></span>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2"><b><?php echo $string[12]; ?></b></td>
				</tr>
				<tr valign="top">
					<td colspan="2" style="padding-left: 15px !important; padding-top: 5px !important; border: 0px;">
						<label for="au" style="display: block;"><input type="checkbox" name="sharebar_automatic" value="1" id="au"<?php if(get_option('sharebar_automatic') == true){echo ' checked';} ?> /><?php echo $string[13]; ?><br/><small><?php echo $string[14]; ?></small></label>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2" style="padding-left: 15px !important; padding-top: 5px !important; border: 0px;">
						<label for="jq" style="display: block;"><input type="checkbox" name="sharebar_include_jquery" value="1" id="jq"<?php if(get_option('sharebar_include_jquery') == true){echo ' checked';} ?> /><?php echo $string[15]; ?><br/><small><?php echo $string[16]; ?></small></label>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2" style="padding-left: 15px !important; padding-top: 5px !important; border: 0px;">
						<label for="fl" style="display: block;"><input type="checkbox" name="sharebar_footer_link" value="1" id="fl"<?php if(get_option('sharebar_footer_link') == true){echo ' checked';} ?> /><?php echo $string[17]; ?><br/><small><?php echo $string[18]; ?></small></label>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2" style="padding-left: 15px !important; padding-top: 5px !important; border: 0px;">
						<label for="everywhere" style="display: block;"><input type="checkbox" name="sharebar_everywhere" value="1" id="everywhere"<?php if(get_option('sharebar_everywhere') == true){echo ' checked';} ?> /><?php echo $string[27]; ?><br/><small><?php echo $string[28]; ?></small></label>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2" style="padding-left: 15px !important; padding-top: 5px !important; border: 0px;">
						<label for="rtl" style="display: block;"><input type="checkbox" name="sharebar_rtl" value="1" id="rtl"<?php if(get_option('sharebar_rtl') == true){echo ' checked';} ?> /><?php echo $string[25]; ?><br/><small><?php echo $string[26]; ?></small></label>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2" style="padding-left: 15px !important; padding-top: 5px !important; border: 0px;">
						<label for="customcss" style="display: block;"><input type="checkbox" name="sharebar_use_customcss" value="1" id="customcss"<?php if(get_option('sharebar_use_customcss') == true){echo ' checked';} ?> /><?php echo $string[37]; ?></label>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2"><b><?php echo $string[36]; ?></b></td>
				</tr>
				<tr valign="top">
					<td colspan="2" style="padding-left: 15px !important; padding-top: 5px !important; border: 0px;">
						<textarea name="sharebar_customcss" style="width: 400px; height: 250px;"><?php echo get_option('sharebar_customcss'); ?></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="padding: 0 !important; margin: 0 !important; border: 0 !important;"><?php submit_button(); ?></td>
				</tr>
			</table>
			</form>
			<table>
				<tr valign="top">
					<td colspan="2"><b><?php echo $string[19]; ?></b></td>
				</tr>
				<tr valign="top">
					<td colspan="2" style="padding-left: 30px !important; padding-top: 0px !important; border: 0px;">
						<div id="iphone">
							<div id="overlay"></div>
							<?php if(get_option('sharebar_use_customcss') == true){ ?>
							<style type="text/css">
							<?php echo get_option('sharebar_customcss'); ?>
							</style>
							<?php } ?>
							<div id="preview_bar">
								<div style="display: block !important; position: absolute !important; z-index: 2 !important;" id="mbl-sharebar" class="sharebar-top mbl-3-buttons"><a href="#" target="_blank" class="sharebar-button sharebar-facebook"><div>...</div></a><a href="#" class="sharebar-button sharebar-twitter"><div>...</div></a><a href="#" class="sharebar-button sharebar-whatsapp"><div>...</div></a><a href="#" class="sharebar-button sharebar-google"><div>...</div></a></div>
							</div>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2" style="padding-top: 68px !important;"><b><?php echo $string[20]; ?></b></td>
				</tr>
				<tr valign="top">
					<td colspan="2" style="padding-left: 15px !important; padding-top: 15px !important; border: 0px; max-width: 400px;">
						<?php echo $string[21]; ?>
						<br/><br/>
						<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank"> <input type="hidden" name="cmd" value="_s-xclick"> <input type="hidden" name="hosted_button_id" value="J38VPY7KB5H7C"> <input type="image" src="<?php echo $string[22]; ?>" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal."> <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1"></form>
					</td>
				</tr>
			</table>
			<table>
				<tr valign="top">
					<td colspan="2"><b>Information</b></td>
				</tr>
				<tr valign="top">
					<td colspan="2" style="padding-left: 15px !important; padding-top: 15px !important; border: 0px;">
						<iframe src="http://somutech.de/somutech-plugins/mobile-sharebar/plg_sidebar.php?locale=<?php echo get_locale().$ping; ?>&version=<?php echo SHAREBAR_VERSION; ?>" frameborder="1" width="300" height="600"></iframe>
					</td>
				</tr>
			</table>

<div id="automatically">
<div class="trenner"></div>
If you don't want to display the Mobile ShareBar automatically, you can use the following codes to embed it yourself:<br/><br/>
<b>Initialize</b><br/>
<pre style="max-width: 650px;">
&lt;script type="text/javascript"> 
	shareBar.init({ 
		"position": "top", //"top" or "bottom"
		"whatsapp": true, //hide or show the WhatsApp Button
		"facebook": true, //hide or show the Facebook Button
		"twitter": true, //hide or show the Twitter Button
		"google": true, //hide or show the Google+ Button
		"url": "YOUR_URL", //Share URL for the Facebook Button
		"text": "CUSTOM_TEXT", //Text for WhatsApp. Don't forget the URL!
		"share_fb": "Share via Facebook", //Facebook Button Text
		"share_wa": "Share via WhatsApp", //WhatsApp Button Text
		"share_g": "Share via Google+", //Google+ Button Text
		"share_tw": "Tweet", //Twitter Button Text
		"rtl": false, //using Semitic(RTL)-Script?
		"everywhere": false //Show button everywhere?
	}); 
&lt;/script>
</pre>
<b>Display after initializing <br/></b><small>Paste it into the footer of your page</small></b><br/>
<pre style="max-width: 280px;">
&lt;script type="text/javascript"> 
	shareBar.show(); 
&lt;/script>
</pre>
<b>Needed Files</b><br/><small>The plugin will add them automatically to your &lt;head> section, but if you deactivate the plugin you have to put them there yourself :(</small><br/>
<pre style="max-width: 950px;">
&lt;link rel="stylesheet" href="<?php echo plugins_url('sharebar.css', __FILE__); ?>" type="text/css" />
&lt;script type="text/javascript" src="<?php echo plugins_url('sharebar.js', __FILE__); ?>">&lt;/script>
</pre>
</div>
<script>
jQuery(document).ready(function() {
  jQuery('pre').each(function(i, e) {hljs.highlightBlock(e)});
  updatePreview();
});
jQuery('#sharebar_admin input').keydown(function(){updatePreview();});
jQuery('#sharebar_admin input').keyup(function(){updatePreview();});
jQuery('#sharebar_admin input').focus(function(){updatePreview();});
jQuery('#sharebar_admin input').blur(function(){updatePreview();});
jQuery('#sharebar_admin input').click(function(){updatePreview();});
jQuery('#sharebar_admin select').change(function(){updatePreview();});
function updatePreview(){
	var shareText_fb = jQuery('input[name="sharebar_buttontext_facebook"]').val();
	var shareText_wa = jQuery('input[name="sharebar_buttontext_whatsapp"]').val();
	var shareText_tw = jQuery('input[name="sharebar_buttontext_twitter"]').val();
	var shareText_g = jQuery('input[name="sharebar_buttontext_google"]').val();
	var buttonCount = 0;
	jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-whatsapp div').html(shareText_wa);
	jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-facebook div').html(shareText_fb);
	jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-twitter div').html(shareText_tw);
	jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-google div').html(shareText_g);
	jQuery('#sharebar_admin #iphone').addClass('bottom');
	jQuery('#sharebar_admin #mbl-sharebar').removeClass('sharebar-top');
	jQuery('#sharebar_admin #mbl-sharebar').removeClass('sharebar-bottom');
		if(jQuery('#sharebar_admin input[name="sharebar_show_whatsapp"]').is(':checked')){
		jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-whatsapp').show();
		buttonCount = buttonCount+1;
	}else{
		jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-whatsapp').hide();
	}
	if(jQuery('#sharebar_admin input[name="sharebar_show_facebook"]').is(':checked')){
		jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-facebook').show();
		buttonCount = buttonCount+1;
	}else{
		jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-facebook').hide();
	}
	if(jQuery('#sharebar_admin input[name="sharebar_show_twitter"]').is(':checked')){
		jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-twitter').show();
		buttonCount = buttonCount+1;
	}else{
		jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-twitter').hide();
	}
	if(jQuery('#sharebar_admin input[name="sharebar_show_google"]').is(':checked')){
		jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-google').show();
		buttonCount = buttonCount+1;
	}else{
		jQuery('#sharebar_admin #mbl-sharebar .sharebar-button.sharebar-google').hide();
	}
	if(jQuery('#sharebar_admin input[name="sharebar_automatic"]').is(':checked')){
		jQuery('#sharebar_admin #automatically').hide();
	}else{
		jQuery('#sharebar_admin #automatically').show();
	}
	if(jQuery('#sharebar_admin #x7').is(':checked')){
		jQuery('#sharebar_admin .cpt').show();
	}else{
		jQuery('#sharebar_admin .cpt').hide();
	}
	if(jQuery('#sharebar_admin select[name="sharebar_bar_position"]').val() == "top"){
		jQuery('#sharebar_admin #iphone').removeClass('bottom');
	}
	jQuery('#sharebar_admin #mbl-sharebar').addClass('sharebar-'+jQuery('#sharebar_admin select[name="sharebar_bar_position"]').val());
	jQuery('#sharebar_admin #mbl-sharebar').removeClass('mbl-1-buttons').removeClass('mbl-2-buttons').removeClass('mbl-3-buttons').addClass('mbl-'+buttonCount+'-buttons');
}
</script>


	</div>
	<?php
	if (!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
}
?>