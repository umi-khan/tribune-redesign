<?php

	/*
	 * $lm_stories is a normal array of stories passed to this template file, this array containes objects of layoutmanagement.story class.
	 * All the stories in array will be shown here through a loop
	 */

	if( is_array($lm_stories) && !empty($lm_stories) )	: ?>

	<ul class="links">

<?php
		foreach($lm_stories as $key => $story) :

			if($key == (count($lm_stories)-1))	$story_html_classes .= ' last';
?>

			<li id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes;?>" >
				<a class="title" href="<?php echo $story->permalink;?>"><?php echo $story->title; ?></a>
			</li>

		<?php endforeach; ?>

		</ul>

<?php endif; ?>