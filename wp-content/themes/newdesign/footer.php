<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

<!-- .site-content -->
		<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="col-xs-12 col-lg-12 mob-logo" style="display:none;">
	<div class="f-log">
    	<a href="/" title="<?php bloginfo('name'); ?>">
    	</a>
    </div>
</div>
<div class="col-xs-12 col-lg-12 mob-footer" style="display:none;">
	<p>
    	&copy; <?php echo date("Y"); ?> </a><?php bloginfo('title'); ?>. </br>
    	<span>Technical feedback? webmaster@tribune.com.pk</span>
    </p>
</div>
		<div id="footer" class="span-24">
		<div class="footer-upper">
			<ul>
			<li><a href="javascript:;"><img src="<?php echo get_template_directory_uri(); ?>/img/fb-icon.png" /></a></li>
			<li><a href="javascript:;"><img src="<?php echo get_template_directory_uri(); ?>/img/tw-icon.png" /></a></li>
			</ul>
		<div class="copyright">tribune.com.pk  © 2015  The Express Tribune</div>
		<ul class="subs">
			<li><a href="javascript:;">Subscribe</a></li>
		</ul>
		</div>
		<div class="content">
			<ul id="footer-navigation" class="clearfix">
				<?php exp_list_categories(); ?>
				<li class="categories last">
					<h5>Others</h5>
					<ul>
						<li class="first"><a href="<?php echo home_url('about/');?>" title="About us">About us</a></li>
					        <li><a href="/advertise/" title="Online Advertising">Online Advertising</a></li>
						<li><a href="<?php echo home_url('rss/');?>" title="Express Tribune RSS Feeds">RSS Feeds</a></li>
	                                        <li><a href="https://plus.google.com/104313813426733151962" title="The Express Tribune Google+ Page" rel="publisher">Google+</a></li>
						<li><a target="_blank" href="<?php echo home_url('subscribe/');?>" title="Subscribe to the Newspaper">Subscribe to the Paper</a></li>
						<li><a href="<?php echo home_url('contact-us/');?>" title="Contact us">Contact us</a></li>
						<li><a href="<?php echo home_url('careers/');?>" title="Careers">Careers</a></li>
						<li class="last"><a href="<?php echo home_url('/styleguide/');?>" title="Style Guide">Style Guide</a></li>					
					        <li><a href="/privacy-policy/" title="Privacy Policy">Privacy Policy</a></li>
						<li><a href="<?php echo home_url('copyrights/');?>" title="Copyrights and Permissions">Copyrights</a></li>
						<li class="last"><a href="<?php echo home_url('code-of-ethics/');?>" title="Code of Ethics">Code of ethics</a></li>					
					</ul>
				</li>
			</ul>
			</div>
		</div><!-- footer -->
		</footer><!-- .site-footer -->
	</div><!-- .site-inner -->
</div><!-- .site -->
<script>
   jQuery(".toggleContent li").hide();
   var size_li = jQuery(".toggleContent li").size();
   var x=1;
   jQuery('.toggleContent li:lt('+x+')').show();
   jQuery('button.toggle').click(function () {
       x= size_li;
       jQuery('.toggleContent li:first-child').addClass('block');
       jQuery('.toggleContent li:lt('+x+')').slideToggle();

   });

</script>
<script type='text/javascript'>
var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];
(function() {
var gads = document.createElement('script');
gads.async = true;
gads.type = 'text/javascript';
var useSSL = 'https:' == document.location.protocol;
gads.src = (useSSL ? 'https:' : 'http:') + 
'//www.googletagservices.com/tag/js/gpt.js';
var node = document.getElementsByTagName('script')[0];
node.parentNode.insertBefore(gads, node);
})();
</script>

<script type='text/javascript'>
googletag.cmd.push(function() {

googletag.defineSlot('/11952262/Tribune_All_970x90', [[728, 90], [970, 250], [970, 90]], 'ad-leaderboard-top').addService(googletag.pubads()).setTargeting("<?php echo $key1; ?>", "<?php echo $target1; ?>");
googletag.defineSlot('/11952262/Tribune_All_300x600', [300, 600], 'ad-box-bottom').addService(googletag.pubads()).setTargeting("<?php echo $key1; ?>", "<?php echo $target1; ?>");
googletag.defineSlot('/11952262/Tribune_All_336x280', [336, 280], 'ad-box-right').addService(googletag.pubads()).setTargeting("<?php echo $key1; ?>", "<?php echo $target1; ?>");
googletag.defineSlot('/11952262/Tribune_Interstitial_800x500', [750, 500], 'div-gpt-ad-1460725010905-0').addService(googletag.pubads()).setTargeting("<?php echo $key1; ?>", "<?php echo $target1; ?>");
googletag.defineSlot('/11952262/Tribune_Inside1_728x90', [728, 90], 'home-small-lb').addService(googletag.pubads()).setTargeting("<?php echo $key1; ?>", "<?php echo $target1; ?>");
googletag.defineSlot('/11952262/Tribune_Inside2_728x90', [728, 90], 'div-gpt-ad-1461927280659-1').addService(googletag.pubads()).setTargeting("<?php echo $key1; ?>", "<?php echo $target1; ?>");
googletag.defineSlot('/11952262/Tribune_Inside3_728x90', [728, 90], 'div-gpt-ad-1461927280659-2').addService(googletag.pubads()).setTargeting("<?php echo $key1; ?>", "<?php echo $target1; ?>");
googletag.defineSlot('/11952262/Tribune_Story_300x250_Top', [300, 250], 'ad-inner-story').addService(googletag.pubads()).setTargeting("<?php echo $key1; ?>", "<?php echo $target1; ?>");
googletag.defineSlot('/11952262/Tribune_Interstitial', [750, 300], 'div-gpt-ad-1462176310948-0').addService(googletag.pubads());
googletag.defineSlot('/11952262/Tribune_All_160x600_Left', [160, 600], 'div-gpt-ad-1462188651185-0').addService(googletag.pubads()).setTargeting("<?php echo $key1; ?>", "<?php echo $target1; ?>");
   googletag.defineSlot('/11952262/Tribune_All_160x600_Right', [160, 600], 'div-gpt-ad-1462188651185-1').addService(googletag.pubads()).setTargeting("<?php echo $key1; ?>", "<?php echo $target1; ?>");
googletag.pubads().enableAsyncRendering();
googletag.enableServices();
});
googletag.cmd.push(function() { googletag.display('ad-box-bottom'); });
googletag.cmd.push(function() { googletag.display('ad-leaderboard-top'); });
googletag.cmd.push(function() { googletag.display('ad-box-right'); });
googletag.cmd.push(function() { googletag.display('home-small-lb'); });
googletag.cmd.push(function() { googletag.display('div-gpt-ad-1461927280659-1'); });
googletag.cmd.push(function() { googletag.display('div-gpt-ad-1461927280659-2'); });
googletag.cmd.push(function() { googletag.display('ad-inner-story'); });
googletag.cmd.push(function() { googletag.display('div-gpt-ad-1462176310948-0'); });
googletag.cmd.push(function() { googletag.display('div-gpt-ad-1462188651185-0'); });
googletag.cmd.push(function() { googletag.display('div-gpt-ad-1462188651185-1'); });
jQuery(document).ready(function() {
googletag.cmd.push(function() { googletag.display('div-gpt-ad-1460725010905-0'); });
});

</script>
<?php wp_footer(); ?>
</body>
</html>
