<?php

	/*
	 * $lm_stories is a normal array of stories passed to this template file, this array containes objects of layoutmanagement.story class.
	 * All the stories in array will be shown here through a loop
	 */

if( is_array($lm_stories) && !empty($lm_stories) )	:

	foreach($lm_stories as $story_num => $story) :
		$image = $story->image_medium;
?>

		<div id="<?php echo $story->html_id;?>" class="<?php echo $story->html_classes; ?>">

			<div class="headline">
				<h2 class="title">
					<a href="<?php echo $story->permalink;?>" ><?php echo $story->title ;?></a>
				</h2>
			</div>

			<a href="<?php echo $story->permalink; ?>">
				<img src="<?php echo $image->src; ?>" class="right sub-story-image" alt="<?php $image->alt; ?>"
					  width="<?php echo $image->width; ?>" height="<?php echo $image->height; ?>" />
			</a>

			<span class="by-line">
				<?php echo $story->author; ?>
				<br/>
				<span class="timestamp" title="<?php echo $story->date_gmt;?>"></span>
			</span>

			<p class="body"><?php echo $story->excerpt;?></p>

			<div>
				<?php
					if($story_num == 0)
					{
						if(function_exists('the_erp_related_posts'))
						{
							the_erp_related_posts($story->id, HEADLINE2_RELATED_STORIES_COUNT);
						}
					}
				?>
			</div>
			<div class="clear"></div>
		</div>

<?php
	endforeach;
endif;
?>