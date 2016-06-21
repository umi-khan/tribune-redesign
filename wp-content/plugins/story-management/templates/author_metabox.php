<div id="sm_authors">
	<div class="add_author_box">
		<div id="add_item_hint">Add New Author</div>
		<p>
			<input type="text" value="" autocomplete="off" size="16" name="sm_new_author_field" id="autocomplete_field" />
			<input type="button" name="sm_add_author_btn" id="add_item_btn" value="Add" class="button" />
			<img alt="" src="<?php echo SM_PLUGIN_URL; ?>images/spinner.gif" id="loading_img" />
			<img alt="" src="<?php echo SM_PLUGIN_URL; ?>images/warning.png" id="warning_img" />
		</p>
	</div>
	
	<div id="selected_items"></div>

	<span id="new_item_content">
		<a href="#" class="item_del_btn">x</a>
	</span>

	<input type="hidden" name="sm_author_post_id" id="sm_author_post_id" value="<?php echo $post_id; ?>" />
</div>