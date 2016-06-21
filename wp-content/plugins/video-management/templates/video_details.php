<li id="vm_gallery_item-<?php echo $video->id; ?>" class="vm_editable vm_deleteable">
	<img class="thumb" alt="<?php echo $video->title; ?>" src="<?php echo $video->thumbnail->url; ?>"
		  title="<?php echo $video->title . '<br />' . $video->caption; ?>" />
</li>