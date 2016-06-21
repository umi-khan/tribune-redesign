<?php
$gallery_display = ($iCounter > 1);

if ($gallery_display)
{
	$template_url	= get_bloginfo('template_url', 'display');
	$seperator 	 	= '####';
	$delay		 	= 5000;
	
	$str_urls 		= implode($seperator, $urls);
	$str_captions	= implode($seperator, $captions);
	
	exp_insert_script("img_show");
}
?>
<div id="exp_img_gallery_box">
	<div id="exp_img_gallery">
		<img id="exp_img_gallery_image" src="<?php echo $urls[0];?>" alt=""/>
	</div>
	<?php if($gallery_display) :?>
		<div id="exp_gallery_wraper" >
			<div id="exp_img_show_navigator" class="navigator">
				<img id="img_gallery_0" src="<?php echo $urls[0];?>" class="active"  alt="" />
				<?php for($iLoop = 1; $iLoop < $iCounter; $iLoop++ ): ?>
					<img id="img_gallery_<?php echo $iLoop;?>" src="<?php echo $urls[$iLoop];?>" class="faded" alt=""/>
				<?php endfor;?>
			</div>
			
		</div>
	<?php endif;?>
	<?php if($captions[0]) : ?>
		<p id="exp_img_gallery_caption" class="caption"><?php echo htmlentities($captions[0]);?></p>
	<?php endif;?>
</div>


<?php if ($gallery_display) :?>
	<script type="text/javascript" language="javascript">
	jQuery(document).ready(function(){
		imgshow_init("<?php echo htmlentities($str_urls);?>", "<?php echo htmlentities($str_captions);?>", "<?php echo $seperator;?>", <?php echo $delay;?>, "<?php echo urlencode($template_url);?>/img/");
	});
	</script>
<?php endif;?>