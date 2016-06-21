<li id="im_gallery_item-<?php echo $image->id; ?>"
	 class="im_manageable im_post_id-<?php echo $image->parent_id; ?> <?php echo $class; ?>">
	<img class="thumb" alt="<?php echo $image->title; ?>" src="<?php echo $image->thumbnail->url; ?>"
		  title="<?php echo $image->title . '<br />' . $image->caption; ?>" />
	<div class="im_additional_details">
		<div id="im_large_img_src-<?php echo $image->id; ?>"><?php echo $image->large->url; ?></div>
	</div>
</li>