 <?php
	global $paged;
	$paged = (int) get_query_var( 'page' );

	$latest_slideshows  = SS_manager::get_latest( 0, $paged );

	if( is_array( $data ) && ! empty( $data ) ) extract( $data );

	if( !empty( $latest_slideshows ) ) :

		$slideshows_page_link = get_category_link( get_cat_ID( 'slideshows' ) );
		
		?>
		<div id="slideshows-gallery" class="gallery last">
			<div class="span-6">
				<h2 class="title">
					<a href="<?php echo $slideshows_page_link; ?>">More Slideshows</a>
				</h2>
			</div>
			<?php if ( $pagination ) :

						$slideshows_per_page = SS_manager::DEFAULT_LIMIT;
						$slideshows_count = SS_manager::get_count(0, 'publish');
						
						exp_paginate_stories_links(
							  array('prev_text' => 'Previous',
								  'next_text' => 'Next',
								  'found_stories' => $slideshows_count,
								  'stories_per_page' => $slideshows_per_page,
								  'seperator' => '',
								  'explainer'=>false)
							  );
					else :
			?>
			<div class="pagination last">
				<a href="<?php echo $slideshows_page_link; ?>">View more</a>
			</div>
			<?php endif; ?>
			<div class="clear"></div>
			<div class="gallery-items clearfix">
				<?php

				$counter = 0;
				$latest_slideshows_count = count($latest_slideshows);

				foreach ($latest_slideshows as $latest_slideshow) :

					$id = $latest_slideshow->id;
					$default_image = $latest_slideshow->default_image;
					$permalink = get_permalink( $id );

					$title = $latest_slideshow->title;
					$counter++;

					if( $counter == 0 ) $class = 'first';
					elseif ( $counter == ( $latest_slideshows_count - 1 ) ) $class = 'last';
					else $class = '';
					?>

					<div class="gallery-item <?php echo $class;?>">
						<a href="<?php echo $permalink;?>" title="<?php echo $title; ?>">
							<img alt="<?php $default_image->caption; ?>" src="<?php echo $default_image->thumbnail->url; ?>" width="150" height="112"/>
						</a>
						<?php exp_edit_story_link( $id );?>
						<p>
							<a href="<?php echo $permalink;?>"><?php echo $title; ?></a>
						</p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
<?php
endif;