<?php

// fetch the current category
$category_id = 0;

if( is_category() )
{
	global $wp_query;
	$current_category = $wp_query->get_queried_object();
	$category_id      = $current_category->cat_ID;
}

// slideshow category page link related
$slideshows_cat_id   = get_category_by_slug( 'slideshows' )->cat_ID;
$slideshows_cat_link = get_category_link( $slideshows_cat_id );

// fetch the latest slideshows
$slideshows = SS_manager::get_latest( $category_id, 0, $num_slideshows );

// set the heading of this group
$heading = ( isset( $heading ) ) ? $heading : 'Latest Slideshows';

?>

<h4>
	<a class="multimedia" href="<?php print $slideshows_cat_link; ?>"><span>RECENT GALLERY</span></a>
</h4>



<div class="content">
		<div class="items clearfix">
			<?php
			
				for( $i = 0, $num_slideshows = count( $slideshows ); $i < $num_slideshows; $i++ ) :
					$slideshow = $slideshows[$i];
					$link      = $slideshows_cat_link . $slideshow->id . '/';
					$title     = esc_html( $slideshow->title );
					$thumbnail = $slideshow->default_image->thumbnail;

					$class = '';
					if( $i == 0 )     $class = 'col-lg-3 col-sm-3 first';
					elseif( $i == 4 ) $class = 'col-lg-3 col-sm-3 last';
					else $class = 'col-lg-3 col-sm-3';
				
			?>
			<div class="item <?php echo $class;?>">
				<a class="image" href="<?php echo $link;?>" title="<?php echo $title; ?>">
					<img alt="" src="<?php echo $thumbnail->url ;?>"
						  width="134" height="100"/>
				</a>
				
				<p><a href="<?php echo $link;?>"><?php echo $title; ?></a></p>
			</div>
			<?php endfor; ?>
		</div>
</div>
