<div class="im_image_editor">
	
	<div>
		<div class="im_viewport_container">
			<h4>Large Image Preview</h4>
			<div id="viewport_<?php echo $image_html_id; ?>" class="im_viewport">
				<img src="" alt="" id="<?php echo $image_html_id; ?>"
					  width="<?php echo $image->full->width; ?>" height="<?php echo $image->full->height; ?>" />
				<div id="thumb_box_<?php echo $image_html_id; ?>" class="im_thumb_box"></div>
			</div>
		</div>

		<div class="im_thumb_container">
			<h4>Thumbnail Preview</h4>
			<div id="thumb_preview_box_<?php echo $image_html_id; ?>" class="im_thumb_preview_box"></div>

			<div class="im_tools">
				<a id="im_thumbbox_toggle_<?php echo $image_html_id; ?>" class="im_thumbbox_toogle"
					title="Show/hide thumbnail area selection tool" href="javascript:void(0);">
				</a>
				<a id="im_thumbbox_maximize_<?php echo $image_html_id; ?>" class="im_thumbbox_maximize"
					title="Default thumbnail area" href="javascript:void(0);">
				</a>
				<br class="clear" />
			</div>

			<div class="im_details">
				<label for="im_title">Title</label>
				<input type="text" class="text" id="im_title" name="im_title" size="32" value="<?php echo $image->title; ?>" />

				<label for="im_caption">Caption</label>
				<textarea id="im_caption" name="im_caption" rows="4" cols="29"><?php echo $image->caption; ?></textarea>
			</div>

			<div class="im_image_editor_btns">
				<input type="button" value="Save Changes" class="button" id="im_save_<?php echo $image->id; ?>" />
			</div>
		</div>

		<div class="clear"></div>
	</div>

	<div id="zoom_slider_<?php echo $image_html_id; ?>" class="im_zoom_slider"></div>

</div>

<script type="text/javascript">
(function($)
{
	var img_id      = '<?php echo $image->id; ?>';
	var img_html_id = '<?php echo $image_html_id; ?>';
	var img_src     = '<?php echo $image->full->url; ?>';
	var crop_info   = null;

	<?php if( $image->thumb_crop_info && $image->large_crop_info ) : ?>

	var thumb_crop_info = <?php echo json_encode($image->thumb_crop_info) ?>;
	var large_crop_info = <?php echo json_encode($image->large_crop_info) ?>;
	crop_info           = $.extend( {}, thumb_crop_info, large_crop_info );

	<?php endif; ?>

	var editor_options = {
		image_id     : img_html_id,
		width        : <?php echo $image_sizes['large']['w']; ?>,
		height       : <?php echo $image_sizes['large']['h']; ?>,
		thumb_width  : <?php echo $image_sizes['thumb']['w']; ?>,
		thumb_height : <?php echo $image_sizes['thumb']['h']; ?>,
		crop_info    : crop_info
	};

	IM_editor.load_image( img_id, img_html_id, img_src, editor_options );
})(jQuery);
</script>