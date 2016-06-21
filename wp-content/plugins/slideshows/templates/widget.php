<div id="slideshows-widget" class="widget">
	<h4><?php echo esc_html($title); ?></h4>
	<div class="content clearfix">
		<div class="controls">
			<img src="<?php echo SLIDESHOWS_PLUGIN_URL; ?>images/left-arrow-thin.gif" alt="&lt;" class="prev disabled" title="Previous" width="16" height="9">
			<img src="<?php echo SLIDESHOWS_PLUGIN_URL; ?>images/right-arrow-thin.gif" alt="&gt;" class="next" title="Next" width="16" height="9">
		</div>
		<div class="carousel ">
			<div class="items">
				<?php
				for( $i = 0, $j = count($slideshows); $i < $j; $i++ ):
					$slideshow = $slideshows[$i];
					$link      = $slideshow_category_link . $slideshow->id . '/';
					$image     = $slideshow->default_image;
					
					if ( $i == 0 )      $class = "first";
					elseif ( $i == $j - 1 ) $class = "last";
					else                $class = "";
				?>

				<div class="item <?php echo $class;?>">
					<a class="image" href="<?php echo $link;?>" title="<?php echo esc_html( $slideshow->title ); ?>">
						<img alt="" src="<?php echo $image->thumbnail->url ;?>" width="134" height="100"/>
					</a>
					<?php edit_post_link( null, '', '', $slideshow->id );?>
					<p><a href="<?php echo $link;?>"><?php echo esc_html( $slideshow->title ); ?></a></p>
				</div>

				<?php endfor; ?>
			</div>
		</div>
	</div>
</div>
