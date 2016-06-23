<?php

$stories       = $data["stories"];
if (!empty($stories) && $stories != false) :

?>
<div id="" class="popular-stories widget">
	<h4>Most Popular</h4>
	<div class="content">
		<?php
		for( $i = 0, $stories_count = count($stories); $i < $stories_count; $i++ ) :
			$story = $stories[$i];
			$story = LM_Story::get_story($story);

			$manager = new IM_Manager( $story->id );
			$video   = false;
			if( false === $manager->has_images() )
			{
				$video_manager = new VM_Manager( $story->id, false );
				$video         = $video_manager->default_video;				
			}

			$image = ( isset( $video ) && false !== $video ) ? $video : $manager->default_image;

			$class = ( $i == $stories_count - 1 ) ? 'last' : '';
		?>
			<div id="<?php echo $story->html_id; ?>" class="story sub-story <?php echo $class;?>">
				<h2 class="title">
					<a href="<?php echo $story->permalink; ?>"><?php echo $story->title;?></a>
				</h2>
				<a href="<?php echo $story->permalink; ?>" class="story-image">
					<img src="<?php echo $image->thumbnail->url; ?>" alt="<?php $image->caption; ?>" width="100" height="75" />
				</a>
				<p class="excerpt"><?php echo $story->excerpt; ?></p>
				<div class="clearfix"></div>
			</div>
		<?php endfor; ?>
	</div>
</div>
<?php endif; ?>