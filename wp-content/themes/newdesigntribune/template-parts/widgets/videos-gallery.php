<?php

global $paged;

// videos page link related
$videos_page_link = get_home_url(null, 'multimedia/videos/');

$title        = 'Latest Videos';
$paged        = (int) get_query_var( 'page' );
$limit        = 8;
$pagination   = false;
$template_url = get_template_directory_uri();
$is_editor    = current_user_can( 'edit_published_posts' ); //Only editor can edit videos

if( is_array( $data ) && ! empty( $data ) ) extract( $data );

if( $pagination )
{
	$total_videos = VM_manager::get_count( 0 );
	$max_pages    = ceil( $total_videos / $limit ) - 1;

	if( $paged > $max_pages ) $paged = 0;

	if( $paged > 0 ) $previous_page = $paged - 1;

	if( $paged < $max_pages ) $next_page = $paged + 1;
}

$videos = VM_manager::get_latest_by_category( 0, $paged, $limit );

$no_of_items_in_row = 4;
$videos_count = count( $videos );
$no_of_rows = ceil ( $videos_count / $no_of_items_in_row );

?>
<div id="videos-gallery" class="gallery widget last">
	<h4>
		<a href="<?php echo $videos_page_link; ?>"><?php echo $title;?></a>
	</h4>
	<div class="pagination last">
	<?php if( false == $pagination ) : ?>
		<a href="<?php echo $videos_page_link . "#videos-gallery"; ?>">View more</a>
	<?php else : ?>
		<?php exp_paginate_stories_links(
				  array(
					  'prev_text'          => 'Previous',
					  'next_text'          => 'Next',
					  'found_stories'      => $total_videos,
					  'stories_per_page'   => $limit,
					  'seperator'          => '',
					  'explainer'          => false,
					  'show_nextprev_link' => false
					  )
				  ); ?>
	<?php endif; ?>
	</div>
	
	<div class="content clear clearfix">
		<div class="gallery-items clearfix">

	<?php for( $i = 0; $i < $no_of_rows; $i++ ) : 				
				
				if( $i == 0 )	$row_class = 'first';
				elseif( $no_of_rows - 1 == $i ) $row_class = 'last';
				else $row_class = '';
	?>
			<div class="clearfix <?php //echo $row_class;?>">

		<?php for( $j = 0; $j < $no_of_items_in_row; $j++ ) :

					$item_index = ( $i * $no_of_items_in_row  ) + $j;

					$video = $videos[$item_index];
					$link  = $videos_page_link . $video->id . '/';
					$title = esc_html( $video->title );

					if( $j == 0 )	$class = 'first';
					elseif( $no_of_items_in_row - 1 == $j ) $class = 'last';
					else $class = '';
				?>
					<div class="gallery-item <?php echo $class;?>">
						<a class="image" href="<?php echo $link;?>" title="<?php echo $title; ?>">
							<img alt="" src="<?php echo $video->thumbnail->url ;?>" width="136" height="102"/>
						</a>
						<?php // exp_edit_story_link( $video->parent_id );?>
						<p><a class="title" href="<?php echo $link;?>"><?php echo $title; ?></a></p>
					</div>
		<?php endfor; ?>
			</div>
	<?php endfor; ?>
		</div>
	</div>
</div>
