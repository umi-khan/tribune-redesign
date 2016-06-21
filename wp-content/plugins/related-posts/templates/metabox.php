<div id="erp_metabox_content">
	<div>
		<div id="erp_search_posts">
			<div class="erp_header">Add related posts</div>
			<p>
				<input type="text" value="" size="16" name="erp_search_field" id="erp_search_field" />
				<input type="button" name="erp_search_posts_btn" id="erp_search_posts_btn" value="Search posts" class="button" />
				&nbsp;during&nbsp;
				<select name="erp_duration" id="erp_duration">
					<option value="1">Last 1 day</option>
					<option value="3" selected="selected">Last 3 days</option>
					<option value="30">Last 1 month</option>
					<option value="90">Last 3 months</option>
				</select>
				<input type="hidden" name="erp_post_id" id="erp_post_id" value="<?php echo $post_id; ?>" />
				<img alt="" src="<?php echo ERP_PLUGIN_URL; ?>images/spinner.gif" id="erp_loading_search_img" class="erp_loading_img" />
			</p>

			<div id="erp_search_results">
				<p>Search results will appear here!</p>
			</div>
		</div>

		<div class="erp_pointer"></div>
		
		<div id="erp_selected_posts">
			<div class="erp_header">Your selections</div>
			<p><?php if( false == is_array( $related_posts ) || count( $related_posts ) < 1 ) : ?>
				You haven't selected any related post yet.
				<?php endif; ?>
			</p>
			<div id="erp_selections">
				<ul>
					<?php foreach( (array)$related_posts as $rp ) : ?>
					<li>
						<a class="erp_del_post_link" id="erppostid-<?php echo $rp->ID; ?>" href="#"></a>
						<span class="erp_post_title"><?php echo $rp->post_title; ?></span> <br />
						<span class="erp_date">
							(Date: <?php echo date( 'F j, Y', strtotime( $rp->post_date ) );?>)
						</span> |
						<a class="erp_post_link" href="<?php echo get_permalink( $rp->ID ); ?>" target="_blank">Link</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div style="text-align: right;">
				<span id="erp_save_selections_msg"></span>
				<img alt="" src="<?php echo ERP_PLUGIN_URL; ?>images/spinner.gif" id="erp_loading_saving_img" class="erp_loading_img" />
				<input type="button" name="erp_save_posts_btn" id="erp_save_posts_btn" value="Save changes" class="button-primary" />
			</div>

		</div>
		<div style="clear: both"></div>
	</div>

	<input type="hidden" name="erp_selections_ids" id="erp_selections_ids" value="" />
</div>