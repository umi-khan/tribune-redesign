<?php

global $paged;
$paged = (int) get_query_var( 'page' );

$latest_slideshows  = SS_manager::get_latest( 0, $paged );

if( is_array( $data ) && ! empty( $data ) ) extract( $data );

if( !empty( $latest_slideshows ) ) :
	$slideshows_page_link = get_category_link( get_cat_ID( 'multimedia' ) ).'/slideshows';

$no_of_items_in_row = 4;
$latest_slideshows_count = count( $latest_slideshows );
$no_of_rows = ceil ( $latest_slideshows_count / $no_of_items_in_row );

?>
	<div id="slideshows-gallery" class="gallery widget last">
		<h4>
			<a href="<?php echo $slideshows_page_link; ?>">More Slideshows</a>
		</h4>

		<?php
		if ( $pagination ) :
			$slideshows_per_page = SS_manager::DEFAULT_LIMIT;
			$slideshows_count = SS_manager::get_count( 0 );

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
		
		<div class="content clear clearfix">
			<div class="clear"></div>
			<div class="gallery-items clearfix">
	<?php for( $i = 0; $i < $no_of_rows; $i++ ) :

				if( $i == 0 )	$row_class = 'first';
				elseif( $no_of_rows - 1 == $i ) $row_class = 'last';
				else $row_class = '';
			?>
			<div class="clearfix <?php echo $row_class;?>">
		
		<?php for( $j = 0; $j < $no_of_items_in_row; $j++ ) :

					$item_index = ( $i * $no_of_items_in_row  ) + $j;

					$latest_slideshow = $latest_slideshows[$item_index];
					$default_image    = $latest_slideshow->default_image;
					
					if( is_object( $default_image->thumbnail ) )
						$image_dimensions = $default_image->thumbnail->smart_dimensions( 136, 102 );
					else
						$image_dimensions = array( 'width' => 136, 'height' => 102 );

					if( $j == 0 )	$class = 'first';
					elseif( $no_of_items_in_row - 1 == $j ) $class = 'last';
					else $class = '';
				?>
				<div class="gallery-item <?php echo $class;?>">
					<a class="image" href="<?php echo $latest_slideshow->permalink; ?>" title="<?php echo $latest_slideshow->title; ?>">
						<img alt="<?php $default_image->caption; ?>" src="<?php echo $default_image->thumbnail->url; ?>"
							  width="<?php echo $image_dimensions['width']; ?>" height="<?php echo $image_dimensions['height']; ?>" />
					</a>
					<?php exp_edit_story_link( $latest_slideshow->id );?>
					<p>
						<a class="title" href="<?php echo $latest_slideshow->permalink; ?>"><?php echo $latest_slideshow->title; ?></a>
					</p>
				</div>
				<?php endfor; ?>
			</div>
		<?php endfor; ?>
			</div>
		</div>
	</div>
<?php
endif;