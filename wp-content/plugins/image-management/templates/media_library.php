<div id="im_media_library">

	<form id="im_media_search_form" action="" method="get">

		<div class="header">
			<h3>Media Library</h3>
			<p class="im_uploadbox">
				<input type="button" id="im_upload_media_btn" value="Upload Image" class="button" />
			</p>
			<p class="im_searchbox">
				<label class="screen-reader-text" for="media-search-input"><?php _e('Search Media');?>:</label>
				<input type="text" id="media-search-input" name="s" value="<?php the_search_query(); ?>" />
				<input id="im_search_media_btn" type="submit" value="<?php esc_attr_e( 'Search Media' ); ?>" class="button" />
			</p>
			<div class="clear"></div>
		</div>

		<div id="im_upload_msg"><p></p></div>
	
		<input type="hidden" name="post_id" value="<?php echo (int)$post_id; ?>" />
		<input type="hidden" name="type" value="<?php echo esc_attr( $type ); ?>" />
		<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>" />
		<input type="hidden" name="post_mime_type" value="<?php echo $post_mime_type; ?>" />
		<input type="hidden" name="paged" value="<?php echo $_GET['paged']; ?>" />

		<?php if( $is_ajax_request ): ?>
		<input type="hidden" name="action" value="im_display_media_library" />
		<?php else: ?>
		<input type="hidden" name="page" value="im-media-library" />
		<?php endif; ?>

		<div class="im_library_navigation">

			<div class="filter_actions">
				<?php
				// months dropdown related
				$months = $wpdb->get_results(
					"SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth
					FROM $wpdb->posts
					WHERE post_type = 'attachment'
					ORDER BY post_date DESC" );

				$month_count = count( $months );

				if ( $month_count && ! ( 1 == $month_count && 0 == $months[0]->mmonth ) ) :
				?>

				<select name='m'>
					<option<?php selected( @$_GET['m'], 0 ); ?> value='0'><?php _e('Show all dates'); ?></option>

					<?php
					foreach ( (array) $months as $arc_row )
					{
						if ( $arc_row->yyear == 0 ) continue;

						$arc_row->mmonth = zeroise( $arc_row->mmonth, 2 );

						if ( isset($_GET['m']) && ( $arc_row->yyear . $arc_row->mmonth == $_GET['m'] ) ) $default = ' selected="selected"';
						else $default = '';

						echo "<option$default value='" . esc_attr( $arc_row->yyear . $arc_row->mmonth ) . "'>";
						echo esc_html( $wp_locale->get_month( $arc_row->mmonth ) . " $arc_row->yyear" );
						echo "</option>\n";
					}
					?>
				</select>

				<?php endif; ?>

				<input type="submit" id="im_filter_media_btn" value="<?php echo esc_attr( __( 'Filter &#187;' ) ); ?>"
						 class="button-secondary" />

			</div>

			<?php
			// pagination related
			$page_links = paginate_links( array(
				'base' => add_query_arg( 'paged', '%#%' ),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => ceil($wp_query->found_posts / IM_library::ITEMS_PER_PAGE),
				'current' => $_GET['paged']
			));

			if ( $page_links ) echo "<div class='im_library_pages'>$page_links</div>";
			?>

			<br class="clear" />
		</div>
	</form>

	<div id="im_library_items">
		<?php
			$count = 0;
			$num_attachments = count( $GLOBALS['wp_the_query']->posts );

			foreach ( (array) $GLOBALS['wp_the_query']->posts as $attachment ) :
				$image = new IM_image_attachment( $attachment->ID );
				$image = new IM_image_attachment( $attachment->ID, $attachment->post_title, $attachment->post_excerpt, $attachment->post_parent );

				$class = ( $count == $num_attachments - 1 ) ? 'last' : '';
				$count++;
		?>
		
		<div class="im_library_item im_post_id-<?php echo $image->parent_id; ?> <?php echo $class; ?>"
			  id="im_library_item-<?php echo $image->id; ?>">
			<img alt="" src="<?php echo $image->thumbnail->url; ?>" class="thumb" />
			<div class="details">
				<div class="title"><?php echo esc_attr( $image->title ); ?></div>
				<div class="caption"><?php echo esc_attr( $image->caption ); ?></div>
			</div>
			<div class="add_media">
				<?php if( false == $is_ajax_request ): ?>
				<input type="button" value="<?php esc_attr_e( 'Edit' ); ?>" class="button edit_library_item" />
				<input type="button" value="<?php esc_attr_e( 'Delete' ); ?>" class="button delete_library_item" />
				<?php else : ?>
				<input type="button" value="<?php esc_attr_e( 'Add to post' ); ?>" class="button add_library_item" />
				<?php endif; ?>
			</div>
			<br class="clear" />
		</div>

		<?php endforeach;?>

	</div>

</div>

<?php if( false == $is_ajax_request ): ?>
<div id="im_popup">
	<div id="im_popup_wrap">
		<div id="im_popup_content"></div>
	</div>
	<img alt="loading" id="im_popup_loading_img" src="<?php echo IMAGE_MANAGEMENT_PLUGIN_URL; ?>images/loading.gif" />
</div>
<?php endif; ?>