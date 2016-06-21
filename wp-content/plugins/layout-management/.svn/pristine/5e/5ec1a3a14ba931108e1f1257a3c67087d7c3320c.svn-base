<?php

	/*
	 * $lm_stories is a normal array of stories passed to this template file, this array containes objects of layoutmanagement.story class.
	 * All the stories in array will be shown here through a loop
	 */

if( is_array($lm_stories) && !empty($lm_stories) )	:

	$main_story	=	array_shift($lm_stories);
	$image = $main_story->image_medium;
?>

	<div id="<?php echo $main_story->html_id;?>" class="<?php echo $main_story->html_classes; ?>">

		<h2 class="title">
			<a href="<?php echo $main_story->permalink;?>" ><?php echo $main_story->title;?></a>
		</h2>

		<a href="<?php echo $story->permalink; ?>">
			<img src="<?php echo $image->src; ?>" class="right sub-story-image" alt="<?php $image->alt; ?>"
				  width="<?php echo $image->width; ?>" height="<?php echo $image->height; ?>" />
		</a>

		<span class="by-line">
			<?php echo $main_story->author; ?>
			<br/>
			<span class="timestamp" title="<?php echo $main_story->date_gmt;?>"></span>
		</span>

		<p class="body"><?php echo $main_story->excerpt;?></p>


		<div>
			<?php
				if( is_array($lm_stories) && !empty($lm_stories) )	:
				?>
					<ul class="links">
					<?php
					foreach( $lm_stories as $key => $story) :
						$html_classes	= $story->html_classes;
					
						if($key == (count($lm_stories)-1))	$html_classes .= ' last';

						?>

						<li id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?>" >
							<a class="title" href="<?php echo $story->permalink;?>"><?php echo $story->title; ?></a>
						</li>


					<?php endforeach; ?>
					</ul>
			<?php endif; ?>
		</div>
		<div class="clear"></div>
	</div>
<?php endif; ?>