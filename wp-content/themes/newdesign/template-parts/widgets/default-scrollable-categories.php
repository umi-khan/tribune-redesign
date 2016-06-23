<?php
	$headlines = $data['categories_headlines'];	
	if(is_array($headlines)) :
		$headlines_count = count( $headlines );
		$width_per_story = 301;
		$margin = 10;
		$onestop_width  = ( ( $width_per_story + $margin ) * $headlines_count ) ;
		$onestop_width  .= 'px';
?>
		<div id="onestop" class="span-24 widget">
			<h1 class="title">One Stop Stories</h1>
			<div class="content clearfix">
				<div class="controls">
					<img src="<?php echo get_template_directory_uri() ?>/img/left-arrow-thin.gif" width="5" height="9" alt="&lt;" class="prev disabled" />
					<img src="<?php echo get_template_directory_uri() ?>/img/right-arrow-thin.gif"  width="5" height="9"  alt="&gt;" class="next" />
				</div>
				<div class="carousel">					
					<div class="items clearfix" style="width:<?php echo $onestop_width; ?>;">
					<?php
						$counter = 0;
						foreach($headlines as $categories_headline) :
							$category	   = $categories_headline['category'];
							$story		   = $categories_headline['post'];
							$image_manager = new IM_Manager( $story->id, false );
							$image         = $image_manager->default_image;
							$counter++;
							if ( $counter == 1 ) $class = "first";
							else if ( $counter == $headlines_count ) $class = "last";
							else $class = "";
					?>
						<div class="item couplet <?php echo $class;?> <?php echo $category->slug; ?>">
							<a href="<?php echo $story->permalink; ?>" class="image">
								<img src="<?php echo $image->thumbnail->url; ?>" alt="<?php $image->caption; ?>" width="150" height="112" />
							</a>
							<p class="excerpt">
								<a href="<?php echo $story->permalink; ?>">
									<?php echo $story->excerpt; ?>
								</a>
							</p>
						</div>
					<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>

<?php endif; ?>