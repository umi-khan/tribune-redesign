<div id="videos-widget" class="widget">
	<h4><?php echo esc_html($title); ?></h4>
	<div class="content clearfix">
		<div class="controls">
			<img src="<?php echo VM_PLUGIN_URL; ?>images/left-arrow-thin.gif" alt="&lt;" class="prev disabled" title="Previous" />
			<img src="<?php echo VM_PLUGIN_URL; ?>images/right-arrow-thin.gif" alt="&gt;" class="next" title="Next" />
		</div>
		<div class="carousel">
			<div class="items">
				<?php
				for( $i = 0, $videos_count = count( $videos ); $i < $videos_count; $i++ ) :
					$video      = $videos[$i];
					$video_link = $video_category_link . $video->id . '/';
					if ( $i == 0 )                      $class = "first";
					else if ( $i == $videos_count - 1 ) $class = "last";
					else                                $class = "";
				?>

				<div class="item <?php echo $class;?>">
					<a class="image" href="<?php echo $video_link;?>" title="<?php echo esc_html( $video->title ); ?>">
						<img alt="" src="<?php echo $video->thumbnail->url ;?>"
							  width="134" height="100"/>
					</a>
					<?php edit_post_link( null, '', '', $video->parent_id );?>
					<p><a href="<?php echo $video_link;?>"><?php echo esc_html( $video->title ); ?></a></p>
				</div>

				<?php endfor; ?>
			</div>
		</div>
	</div>
</div>