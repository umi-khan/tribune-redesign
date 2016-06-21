<?php
	global $paged;
	$paged = (int) get_query_var( 'page' );

	$slideshows  = SS_manager::get_latest( 0, $paged );

	if( !empty( $slideshows ) ) :
		$slideshows_page_link = get_category_link( get_cat_ID( 'multimedia' ) ).'slideshows';

	$slideshows_count = count( $slideshows );?>

	<div id="more-slideshows" class="more-slideshows widget">
		<h4>
			<a href="<?php echo $slideshows_page_link; ?>">More Slideshows</a>
		</h4>
		<div class="content clearfix">
		<?php
		$counter = 0;

		foreach( $slideshows as $slideshow ) :

			$image_dimensions = array( 'width' => 100, 'height' => 75 );

			$default_image = $slideshow->default_image;
			if( $default_image->thumbnail )
				$image_dimensions = $default_image->thumbnail->smart_dimensions( $image_dimensions['width'], $image_dimensions['height'] );

			$class = ( $counter++ == 0 ) ? 'first' : ( $counter == $slideshows_count ? 'last' :  '' ) ;

			?>

			<div class="story <?php echo $class;?>" id="id-<?php echo $slideshow->id;?>">
				<div class="content clearfix">
					<a class="image" href="<?php echo $slideshow->permalink; ?>" title="<?php echo $slideshow->title; ?>">
						<img alt="<?php $default_image->caption; ?>" src="<?php echo $default_image->thumbnail->url; ?>"
							  width="<?php echo $image_dimensions['width']; ?>" height="<?php echo $image_dimensions['height']; ?>" />
					</a>

					<h2 class="title">
						<a title="<?php echo $slideshow->title; ?>" href="<?php echo $slideshow->permalink; ?>"><?php echo $slideshow->title; ?></a>						
					</h2>

					<div class="meta">
						<?php exp_comments_link( $slideshow->id ); ?>
						<span class="timestamp" title="<?php echo $slideshow->date_gmt;?>"></span>
					</div>
				</div>
			</div>

		<?php
		endforeach; ?>

		</div>

		<?php
		$slideshows_per_page = SS_manager::DEFAULT_LIMIT;
		$total_slideshows    = SS_manager::get_count( 0 );

		exp_paginate_stories_links(
				  array(
					  'prev_text'        => 'Previous',
					  'next_text'        => 'Next',
					  'found_stories'    => $total_slideshows,
					  'stories_per_page' => $slideshows_per_page,
					  'seperator'        => '',
					  'explainer'        => false,
					  'show_each_side'   => 2
					  )
				  );
		?>

	</div>
<?php endif; ?>