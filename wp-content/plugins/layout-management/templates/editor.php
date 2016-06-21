<div id="lm_popup">
	<div id="lm_tab_container" class="clearfix">
		<ul class="lm_tabs">
			<li><a href="#lm_quick_editor" class="current">Quick Editor</a></li>
			<li><a href="#lm_layout_manager">Layout Manager</a></li>
		</ul>
		<div class="lm_tabs_content_group clearfix">
			<?php include( $quickeditor_template ); ?>
			<?php include( $layouteditor_template ); ?>
		</div>
	</div>

	<?php include( $storyeditor_template ); ?>

	<?php include( $storysetter_template ); ?>
</div>