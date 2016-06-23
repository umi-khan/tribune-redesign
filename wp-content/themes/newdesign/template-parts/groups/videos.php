<?php

// video category page link related
$video_cat_id   = get_category_by_slug( 'videos' )->cat_ID;
$video_cat_link = get_category_link( $video_cat_id );

// fetch the latest videos
$videos = VM_manager::get_latest_by_category( 0, 0, $num_videos );

?>

<h4>
	<a class="multimedia" href="<?php print $video_cat_link; ?>"><span>Videos</span></a>
</h4>


<div class="content">	

		<div class="items clearfix">
			<?php
			
				for( $i = 0, $num_videos = 5; $i < $num_videos; $i++ ) :
					$video = $videos[$i];
					$link  = $video_cat_link . $video->id . '/';
					$title = esc_html( $video->title );
					$class = '';

					if( $i == 0 )               $class = 'first';
					elseif( $i == 1 ) $class = 'pad-left col-lg-3 col-xs-12 col-sm-3';
					elseif( $i == 4 ) $class = 'col-xs-12 col-lg-3 col-sm-3 last';
					else $class = 'col-lg-3 col-xs-12 col-sm-3';
				
			?>
			<div class="item <?php echo $class;?>" >
				<a class="image" href="<?php echo $link;?>" title="<?php echo $title; ?>">
					<img alt="" src="<?php echo $video->thumbnail->url ;?>"
						  width="134" height="100"/>
				</a>
				
				<p><a href="<?php echo $link;?>"><?php echo $title; ?></a></p>
			</div>
			<?php endfor; ?>
	</div>
</div>