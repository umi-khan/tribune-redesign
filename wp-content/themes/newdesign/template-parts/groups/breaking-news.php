<?php

if( false == isset( $category_id ) ) return;

$special_story_layout = new LM_layout( $category_id, LM_config::GROUP_SPECIAL_STORY );

if( count( $special_story_layout->posts_stack ) < 1 ) return;

?>

<div class="breaking-news clearfix group last">
	<div class="span-16">
		<?php
			$story = new Control_special_story( array_shift( $special_story_layout->posts_stack ) );
			$story->display();
		?>
	</div>
</div>