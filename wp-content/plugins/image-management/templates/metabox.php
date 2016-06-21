<div id="im_gallery">

	<div id="im_thumbnails">

		<a id="im_upload" href="javascript: void(0);" class="im_post_id-<?php echo $post_id; ?>">
			<img alt="Add an image" src="<?php echo IMAGE_MANAGEMENT_PLUGIN_URL; ?>images/add.png?v=0.1" />
		</a>

		<div id="im_scrollable">
			<ul class="items">

				<?php
					foreach( (array)$post_images->images as $image ) :

						$class = '';

						// is this the default image
						if( $post_images->is_default( $image ) ) $class = 'active';
				?>

				<li id="im_gallery_item-<?php echo $image->id; ?>"
					 class="im_manageable im_editable im_post_id-<?php echo $post_id; ?> <?php echo $class; ?>">
					<img class="thumb" alt="<?php echo $image->title; ?>" src="<?php echo $image->thumbnail->url; ?>"
						  title="<?php echo $image->title . '<br />' . $image->caption; ?>" />
					<div class="im_additional_details">
						<div id="im_large_img_src-<?php echo $image->id; ?>"><?php echo $image->large->url; ?></div>
					</div>
				</li>

				<?php	endforeach;	?>
			</ul>
		</div>

		<div class="clear"></div>

	</div>

</div>

<div id="im_popup">
	<div id="im_popup_wrap">
		<div id="im_popup_content"></div>
	</div>
	<img alt="loading" id="im_popup_loading_img" src="<?php echo IMAGE_MANAGEMENT_PLUGIN_URL; ?>images/loading.gif" />
</div>

<img alt="delete" id="im_delete_img" src="<?php echo IMAGE_MANAGEMENT_PLUGIN_URL; ?>images/cancel.png" />
<img alt="set as default" id="im_set_default_img" src="<?php echo IMAGE_MANAGEMENT_PLUGIN_URL; ?>images/accepted.png" />

<input type="hidden" id="im_post_id" name="im_post_id" value="<?php echo (int)$post_id; ?>" />