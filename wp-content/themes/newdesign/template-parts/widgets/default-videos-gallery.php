<?php

	global $paged;

	// video category page link related
	$video_category_id = get_category_by_slug( 'videos' )->cat_ID;
	if( $video_category_id ) $video_category_link = get_category_link( $video_category_id );
	
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

?>

<div id="videos-gallery" class="gallery last">
	<div class="span-6">
		<h2 class="title">
			<a href="<?php print $video_category_link; ?>"><?php echo $title;?></a>
		</h2>
	</div>
	<div class="pagination last">
		<?php if( false == $pagination ) : ?>		
			<a href="<?php print $video_category_link . "#videos-gallery"; ?>">View more</a>		
		<?php else : ?>
			<?php exp_paginate_stories_links(
					  array('prev_text' => 'Previous',
						  'next_text' => 'Next',
						  'found_stories' => $total_videos,
						  'stories_per_page' => $limit,
						  'seperator' => '',
						  'explainer'=>false)
					  ); ?>
		<?php endif; ?>
	</div>
	<div class="gallery-items clearfix">
		<?php
			$counter      = 0;
			$num_videos = count( $videos );
			
			foreach( $videos as $video ) :
				$link      = $video_category_link . $video->id . '/';
				$title     = esc_html( $video->title );

				$counter++;
				if( $counter == 1 )               $class = ' first';
				elseif( $counter == $num_videos ) $class = ' last';
				else                              $class = '';
			?>
			<div class="gallery-item<?php echo $class;?>">
				<a href="<?php echo $link;?>" title="<?php echo $title; ?>">
					<img alt="" src="<?php echo $video->thumbnail->url ;?>" width="150" height="112"/>
				</a>
				<?php exp_edit_story_link( $video->parent_id );?>
				<p><a href="<?php echo $link;?>"><?php echo $title; ?></a></p>
			</div>
		<?php
			endforeach;
		?>
	</div>
</div>