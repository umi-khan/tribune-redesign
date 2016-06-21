<?php

$lifestyle_cat    = get_category_by_slug( 'life-style' );
$lifestyle_layout = new LM_layout( $lifestyle_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 5 );

?>

<h3 class="m-group">
	<a href="<?php echo get_category_link( $lifestyle_cat->cat_ID ); ?>"><?php echo $lifestyle_cat->cat_name; ?></a>
</h3>	
	<?php
	$small_story_args = array();
	for( $i = 0; $i < 4; $i++ )
	{
		$small_story_args['is_first'] = $i == 0;
		$small_story_args['is_last']  = $i == 4 - 1;

		$story = new Control_small_story( array_shift( $lifestyle_layout->posts_stack ) );
		$story->display( $small_story_args );
	}
	?>
