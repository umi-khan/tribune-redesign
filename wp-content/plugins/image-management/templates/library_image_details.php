<div class="im_library_item" id="im_library_item-<?php echo $image->id; ?>">
	<img alt="" src="<?php echo $image->thumbnail->url; ?>" class="thumb" />
	<div class="details">
		<div class="title"><?php echo $image->title; ?></div>
		<div class="date">Added on <?php echo $display_date; ?></div>
	</div>
	<div class="add_media">
		<input type="button" value="<?php esc_attr_e( 'Edit' ); ?>" class="button edit_library_item" />
		<input type="button" value="<?php esc_attr_e( 'Delete' ); ?>" class="button delete_library_item" />
	</div>
	<br class="clear" />
</div>