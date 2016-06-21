<div id="lm_quick_editor" class="lm_tabs_content clearfix">
	<div id="quickeditor_message" class="clear"><p></p></div>
	<p>
		<label for="lm_quick_editor_title">Title</label>
		<input type="text" class="text" id="lm_quick_editor_title" value="" />
	</p>
	<p>
		<label for="lm_quick_editor_excerpt">
			Excerpt <span class="lm_excerpt_chars_counter">Characters remaining: <span></span></span>
		</label>
		<span class="lm_excerpt_error_content"></span>
		<textarea id="lm_quick_editor_excerpt" class="text"></textarea>
	</p>

	<div class="lm_quickeditor_submit">
		<input id="quickeditor_submit_button" class="submit" type="button" name="submit_btn" value="Update story" />
		<span class="links">
			<a id="lm_quick_editor_edit_link" target="_blank" href="<?php echo admin_url( 'post.php?action=edit&post=' ); ?>">or edit the story completely.</a>
		</span>
	</div>
</div>