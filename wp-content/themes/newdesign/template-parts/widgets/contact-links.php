<?php 
 $template_url = get_bloginfo('template_url', 'display');
 $site_name    = esc_html( get_bloginfo('name') );
 $home_url     = home_url();
 
 if(!empty($data))
 {
	  $facebook_widget_type = $data['facebook_widget_type'];
	  $facebook_page_id     = $data['facebook_page_id'];
	  $facebook_app_id      = $data['facebook_app_id'];

	  $width  = $data['width'];
	  $height = $data['height'];

	  $connections = intval($data['connections']);

	  $stream = (isset( $data['stream']) && $data['stream']==1 ) ? 'true' : 'false';
	  $header = (isset( $data['header']) && $data['header']==1 )? 'true' : 'false';

	  if( ! empty ( $facebook_page_id ) ) exp_insert_script( 'fb-sdk' );
 }
?>
<div class="sidebar-right-child connect-links">
	<h3>Stay Connected</h3>
	<?php if($facebook_widget_type) :?>
		<?php if($facebook_widget_type == 'likebox'):?>
			<?php if(!empty($facebook_page_id)) :?>				
				<fb:like-box profile_id="<?php echo $facebook_page_id;?>" width="<?php echo $width;?>" height="<?php echo $height;?>" connections="<?php echo $connections;?>" stream="<?php echo $stream;?>" header="<?php echo $header;?>"></fb:like-box>
			<?php endif;?>
		<?php endif;?>
		<?php if($facebook_widget_type == 'livestream'):?>
			<?php if(!empty($facebook_app_id)):?>
				<fb:live-stream event_app_id="<?php echo $facebook_page_id;?>" width="<?php echo $width;?>" height="<?php echo $height;?>"></fb:live-stream>
			<?php endif;?>
		<?php endif;?>
	<?php endif;?>
	<ul>
	  <li><a href="<?php echo $home_url;?>/feed/rss" target="_blank" title="Subscribe to RSS feed"><img src="<?php echo $template_url; ?>/img/rss-square.png" alt="rss" />Subscribe to RSS feed</a></li>	  
	  <li class="last"><a href="http://www.twitter.com/etribune" target="_blank" title="Follow <?php echo $site_name; ?> on Twitter"><img src="<?php echo $template_url; ?>/img/twitter.png" alt="twitter" />Follow <?php echo $site_name; ?></a></li>
	  <!-- <li class="last" ><a id="newsletterpopup" name="newsletterpopup" href="<?php $home_url; ?>/newsletter" title="Signup for Newsletter" ><img src="<?php echo $template_url; ?>/img/email.png" alt="email" />Signup for Newsletter</a></li> -->
	</ul>

</div>