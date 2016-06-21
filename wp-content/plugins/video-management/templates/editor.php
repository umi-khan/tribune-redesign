<div class="vm_video_editor">
	
	<div class="vm_video_container">
		<h4>Video</h4>
		<div class="vm_video"><?php $video->player( 600, 450 ); ?></div>
	</div>

	<div class="vm_thumb_container">
		<h4>Thumbnail</h4>
		<img class="thumb" src="<?php echo $video->thumbnail->url; ?>" alt="<?php $video->title; ?>"
			  width="<?php echo $video->thumbnail->width; ?>" height="<?php echo $video->thumbnail->height; ?>" />

		<div class="vm_details">
			<label for="vm_title">Title</label>
			<input type="text" class="text" id="vm_title" name="vm_title" size="32" value="<?php echo $video->title; ?>" />

			<label for="vm_caption">Caption</label>
			<textarea id="vm_caption" name="vm_caption" rows="4" cols="29"><?php echo $video->caption; ?></textarea>
		</div>

		<div class="vm_edit_btns">
			<input type="button" value="Save Changes" class="button" id="vm_edit_btn" />
			<img alt="loading" id="vm_loading_img" src="<?php echo admin_url('images/wpspin_light.gif'); ?>" />
		</div>
	</div>

	<div class="clear"></div>

</div>