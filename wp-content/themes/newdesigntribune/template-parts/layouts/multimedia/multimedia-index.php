<?php

$video = @array_pop( VM_manager::get_latest_by_category(0, 0, 1) );
$story = LM_story::get_story($video->parent_id);



get_header();

?>
<div class="multimedia">
		<?php if($video) : ?>
		<div class="story" id="id-<?php echo $video->parent_id; ?>">
			<h1 class="title">
			  <a href='<?php echo $story->permalink; ?>'><?php echo $video->title;?></a>
			</h1>
		
			<?php $video->player(620,349); ?>
		</div>
		<?php endif; ?>

		<?php exp_load_widget_file( "videos-gallery" , array( 'pagination' => false , 'limit' => 8 ) ); ?>
		
		<?php exp_load_widget_file( "slideshow-gallery" , array( 'pagination' => false  ) ); ?>

</div>
</div>