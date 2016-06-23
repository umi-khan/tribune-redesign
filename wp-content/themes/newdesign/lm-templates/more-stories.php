<?php if( is_array($lm_stories) && !empty($lm_stories) )	: ?>

<ul class="links">

<?php
	for( $i = 0, $j = count( $lm_stories ); $i < $j; $i++ ):
		$story = $lm_stories[$i];

		$story_html_classes = $story->html_classes;
		if( $i == 0 )      $story_html_classes .= ' first';
		if( $i == $j - 1 ) $story_html_classes .= ' last';
?>

	<li id="<?php echo $story->html_id; ?>" class="<?php echo $story_html_classes; ?>" >
		<a href="<?php echo $story->permalink;?>"><?php echo $story->title; ?></a>
	</li>

<?php endfor; ?>

</ul>

<?php endif; ?>