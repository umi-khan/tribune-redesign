<?php
/* shortcodes */
add_shortcode('kn-post-url', 'kn_post_url');
function kn_post_url() {
    return get_permalink();
}
add_shortcode('kn-post-title', 'kn_post_title');
function kn_post_title() {
    return get_the_title();
}

/* menus */
if ( is_admin() ){
	add_action('admin_menu', 'kn_mobile_sharebar_menu');
		function kn_mobile_sharebar_menu() {
			add_submenu_page('options-general.php', 'Express Mobile Sharebar', 'Express Mobile Sharebar', 'administrator', 'kn_mobile_sharebar_settings', 'kn_mobile_sharebar_page');
			add_action( 'admin_init', 'register_kn_mobile_sharebar_settings' );
		}
}

/* settings */
function register_kn_mobile_sharebar_settings() {
	register_setting( 'kn_mobile_sharebar_group', 'kn_mobile_sharebar_where' );
	register_setting( 'kn_mobile_sharebar_group', 'kn_mobile_sharebar_twitter' );
	register_setting( 'kn_mobile_sharebar_group', 'kn_mobile_sharebar_whatsapp' );
	register_setting( 'kn_mobile_sharebar_group', 'kn_mobile_sharebar_visibility_homepage' );
	register_setting( 'kn_mobile_sharebar_group', 'kn_mobile_sharebar_visibility_post' );
	register_setting( 'kn_mobile_sharebar_group', 'kn_mobile_sharebar_visibility_page' );
	register_setting( 'kn_mobile_sharebar_group', 'kn_mobile_sharebar_height' );
	register_setting( 'kn_mobile_sharebar_group', 'kn_mobile_small_desktop' );
}

/* page */
function kn_mobile_sharebar_page() {
?>
<div class="wrap">
	<h2>Mobile Sharebar Settings</h2>
	<form method="post" oninput="amount.value=kn_mobile_sharebar_height.value" action="options.php">
		<?php settings_fields( 'kn_mobile_sharebar_group' ); ?>
		<?php do_settings_sections( 'kn_mobile_sharebar_group' ); ?>
		
<table class="form-table">
	<tbody>
		<!-- twitter -->

		
		<!-- whatsapp -->


		<!-- visibility -->
		<tr>
			<th scope="row"><label for="blogname">Sharebar Visibility</label></th>
			<td>
			<fieldset>
					<label for="kn_mobile_sharebar_visibility_homepage">
						<input name="kn_mobile_sharebar_visibility_homepage" type="checkbox" id="kn_mobile_sharebar_visibility_homepage" <?php if(get_option( 'kn_mobile_sharebar_visibility_homepage' )=="on"){ echo "checked='checked'"; } ?>> Show Express Mobile Sharebar at frontpage.
					</label>
					<br>
					<label for="kn_mobile_sharebar_visibility_post">
						<input name="kn_mobile_sharebar_visibility_post" type="checkbox" id="kn_mobile_sharebar_visibility_post" <?php if(get_option( 'kn_mobile_sharebar_visibility_post' )=="on"){ echo "checked='checked'"; } ?>> Show Express Mobile Sharebar at every blog post.
					</label>
					<br>
					<label for="kn_mobile_sharebar_visibility_page">
						<input name="kn_mobile_sharebar_visibility_page" type="checkbox" id="kn_mobile_sharebar_visibility_page" <?php if(get_option( 'kn_mobile_sharebar_visibility_page' )=="on"){ echo "checked='checked'"; } ?>> Show Express Mobile Sharebar at every page.
					</label>
					<br>
					<label for="kn_mobile_small_desktop">
						<input name="kn_mobile_small_desktop" type="checkbox" id="kn_mobile_small_desktop" <?php if(get_option( 'kn_mobile_small_desktop' )=="on"){ echo "checked='checked'"; } ?>> Show Express Mobile Sharebar on small size desktop browser. <em>( good for testing purpose )</em>
					</label>
			</fieldset>
			</td>
		</tr>

		<!-- height -->
		<tr>
			<th scope="row"><label for="blogname">Sharebar Height</label></th>
			<td>
			<input id="kn_mobile_sharebar_height" name="kn_mobile_sharebar_height" type ="range" min ="25" max="75" step="1" value="<?php echo get_option( 'kn_mobile_sharebar_height' ); ?>"/>
			( <em>set to : height=<output name="amount" for="kn_mobile_sharebar_height"><?php echo get_option( 'kn_mobile_sharebar_height' ); ?></output>px;</em> )
			<br><br>
			<ul style="list-style: none;padding: 0 0 0 0;margin: 0 0 0 0;height: 50px;width: 100%;display: table;">
				<li style="display: table-cell;background-color: #3b5998;padding: 0 0 0 0;margin: 0 0 0 0;height: 50px;width: 33.3333333333%;background-image: url('<?php echo kakinetwork_url; ?>images/facebook.png');background-size: contain;background-repeat: no-repeat;background-position: center;"><img id="img" src="<?php echo kakinetwork_url; ?>images/blank.png" width="<?php echo get_option( 'kn_mobile_sharebar_height' ); ?>px"/></li>
				<li style="display: table-cell;background-color: #3eaefb;padding: 0 0 0 0;margin: 0 0 0 0;height: 50px;width: 33.3333333333%;background-image: url('<?php echo kakinetwork_url; ?>images/twitter.png');background-size: contain;background-repeat: no-repeat;background-position: center;"><img id="img" src="<?php echo kakinetwork_url; ?>images/blank.png"/ width="<?php echo get_option( 'kn_mobile_sharebar_height' ); ?>px"></li>
				<li style="display: table-cell;background-color: #2ab200;padding: 0 0 0 0;margin: 0 0 0 0;height: 50px;width: 33.3333333333%;background-image: url('<?php echo kakinetwork_url; ?>images/whatsapp.png');background-size: contain;background-repeat: no-repeat;background-position: center;"><img id="img" src="<?php echo kakinetwork_url; ?>images/blank.png"/ width="<?php echo get_option( 'kn_mobile_sharebar_height' ); ?>px"></li>
			</ul>
			
			<p class="description">Minimum height 25px and maximum height 75px</p>
			</td>
		</tr>
<script>
var ranger = document.getElementById('kn_mobile_sharebar_height');
var image =  document.getElementById('img');
var width = image.width;
var height = image.height;

ranger.onchange = function(){
    image.width = width * (ranger.value / 100);
    image.height = height * (ranger.value / 100);
}
</script>

		<!-- where -->
		<tr>
			<th scope="row"><label for="blogname">Sharebar Location</label></th>
			<td>
			<select name="kn_mobile_sharebar_where" id="kn_mobile_sharebar_where">
<!-- 			  <option <?php if(get_option( 'kn_mobile_sharebar_where' )=="top") { echo "selected"; } ?> value="top">Top of the Page</option>
 -->			  <option <?php if(get_option( 'kn_mobile_sharebar_where' )=="bottom") { echo "selected"; } ?> value="bottom">Bottom of the Page</option>
			</select>
			<p class="description">Where to place this Mobile Sharebar?</p>
			</td>
		</tr>
	</tbody>
</table>
		<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</form>
</div>
<?php } ?>