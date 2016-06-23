<?php
if( is_array($lm_stories) && !empty($lm_stories) )	:

	foreach($lm_stories as $story):
		$image_manager    = new IM_Manager( $story->id, false );
		$image            = $image_manager->default_image;
		$image_dimensions = $image->large->smart_dimensions( 305, 229 );
		//$author           = SM_author_manager::get_author_posts_link( $story->id );
		$images_count     = count( $image_manager->images );

		if( $images_count == 0 )
		{
			$video_manager = new VM_Manager( $story->id );
			$default_video = $video_manager->default_video;
		}
?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?> top top-story clearfix">
	<div class="content">
		<div class="col-lg-6">
	      <h1 class="title"><a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a></h1>

			<div class="social">
				<?php
				exp_comments_link($story->id);
				exp_addthis_button($story->id, $story->permalink, $story->title);				
				?>
				<a class="email-link" rel="nofollow" href="<?php // echo exp_get_email_link( $story ); ?>"
					title="Email story to friend" target="_blank" >Email</a>				
			</div>

			<div class="meta">
				<span class="author"><?php echo $author;?></span>
				<span class="timestamp" title="<?php echo $story->date_gmt;?>"></span>
			</div>

			<p class="excerpt">
				<?php echo $story->excerpt;?>
			</p>
			
			<?php if(function_exists('the_erp_related_posts')) the_erp_related_posts($story->id, 2, true, false, false); ?>
		</div>
		
		<div class="col-lg-6 last">
			<?php
			if( isset( $default_video ) && false !== $default_video ) : ?>
				<div id="story-video" >
					<?php
					$default_video->player( $image_dimensions['width'], $image_dimensions['height'] ); ?>
					<p class="caption">
						<?php echo $default_video->caption; ?>
					</p>
				</div>
			<?php
			else : ?>
				<a class="top-story-image" href="<?php echo $story->permalink; ?>">
					<img src="<?php echo $image->large->url; ?>" alt="<?php $image->caption; ?>"
						  width="<?php echo $image_dimensions['width']; ?>" height="<?php echo $image_dimensions['height']; ?>" />
				</a>
			<?php
			endif; ?>
		</div>
	</div>
</div>

<?php
	endforeach;
endif;
?>