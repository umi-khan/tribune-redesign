<?php

if( is_array($lm_stories) && !empty($lm_stories) )	:

	foreach($lm_stories as $story_num => $story) :
		$image_manager = new IM_Manager( $story->id, false );
		$image         = $image_manager->default_image;
		//$author        = SM_author_manager::get_author_posts_link( $story->id );
		$images_count     = count( $image_manager->images );

		if( $images_count == 0 )
		{
			$video_manager = new VM_Manager( $story->id );
			$default_video = $video_manager->default_video;
		}
?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?> sub-story clearfix">
	<h2 class="title"><a href="<?php echo $story->permalink; ?>" ><?php echo $story->title; ?></a></h2>

	<a class="sub-story-image" href="<?php echo $story->permalink; ?>">
		<?php
		if( isset( $default_video ) && false !== $default_video ) : ?>
			<img src="<?php echo $default_video->thumbnail->url; ?>" alt="<?php echo $default_video->caption; ?>" width="160" height="120" />
		<?php
		else : ?>
		<img src="<?php echo $image->thumbnail->url; ?>" alt="<?php echo $image->caption; ?>" width="160" height="120" />
		<?php
		endif; ?>
	</a>

	<div class="meta">
		<span class="author"><?php echo $author;?></span>
		<span class="timestamp" title="<?php echo $story->date_gmt;?>"></span>
	</div>

	<p class="excerpt"><?php echo $story->excerpt;?></p>
</div>

<?php
	endforeach;
endif;
?>