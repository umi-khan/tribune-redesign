<div class="videos-single-widget widget">
	<h4><?php echo esc_html($title); ?></h4>
	<div class="content">
		<?php

			$is_editor = current_user_can('edit_published_posts'); //Only editor can edit videos
			$counter   = 0;
			foreach( $post_videos->videos as $video ) :
				if( $video->id != $video_to_hide ) :
					$video_link  = $video_category_link . $video->id . '/';
					$video_title = html_entity_decode( $video->title, ENT_QUOTES );
					$video_title = VM_manager::trim_content( $video_title, 56 );
					$video_title = VM_manager::word_break( $video_title );
					$video_title = esc_html($video_title);

					$class = ( $counter++ == 0 ) ? 'first' : ( $counter == $post_videos_count ? 'last' : '' );
		?>
				<div class="video clearfix <?php echo $class; ?>">
					<a class="image" href="<?php echo $video_link; ?>">
						<img src="<?php echo $video->thumbnail->url ?>" width="120" height="90" alt="" />
					</a>
					<p class="excerpt">
						<a href="<?php echo $video_link; ?>"><?php echo $video_title; ?></a>
						<?php if( $is_editor ): ?>
							<a target="_blank" href="<?php echo get_edit_post_link( $video->parent_id );?>" >
								<img class="video_edit_img" src="<?php echo VM_PLUGIN_URL;?>images/edit.png"  alt="" />
							</a>
						<?php endif; ?>
					</p>
				</div>
			<?php
				endif;
			endforeach; ?>
	</div>
</div>