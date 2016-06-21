<input type="hidden" id="vm_post_id" name="vm_post_id" value="<?php echo $post_id; ?>" />

<div id="vm_gallery">

	<div id="vm_thumbnails">

		<a id="vm_add_video" href="javascript: void(0);">
			<img alt="Add a video" src="<?php echo VM_PLUGIN_URL; ?>images/add.png" />
		</a>

		<div id="vm_scrollable">
			<ul class="items">

				<?php
					foreach( (array)$post_videos->videos as $video ) :

						$class = '';

						// is this the default video
						if( $post_videos->is_default( $video ) ) $class = 'active';
				?>

				<li id="vm_gallery_item-<?php echo $video->id; ?>" class="vm_editable vm_manageable <?php echo $class; ?>">
					<img class="thumb" alt="<?php echo $video->title; ?>" src="<?php echo $video->thumbnail->url; ?>"
						  title="<?php echo $video->title . '<br />' . $video->caption; ?>" />
				</li>

				<?php	endforeach;	?>
			</ul>
		</div>

		<div class="clear"></div>

	</div>

</div>

<div id="vm_popup">
	<div id="vm_popup_wrap">
		<div id="vm_popup_content"></div>
	</div>	
	<img alt="loading" id="vm_popup_loading_img" src="<?php echo VM_PLUGIN_URL; ?>images/loading.gif" />
</div>

<img alt="delete" id="vm_delete_img" src="<?php echo VM_PLUGIN_URL; ?>images/cancel.png" />
<img alt="set as default" id="vm_set_default_img" src="<?php echo VM_PLUGIN_URL; ?>images/accepted.png" />