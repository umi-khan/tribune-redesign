<?php

$num_long_stories = 4;

$business_cat    = get_category_by_slug( 'business' );
$business_layout = new LM_layout( $business_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 9 );

$business_cat_link = get_category_link( $business_cat->cat_ID );

?>

<h3 class="m-group">
	<a href="<?php echo $business_cat_link; ?>"><?php echo $business_cat->cat_name; ?></a>
</h3>
	<?php
	$small_story_args = array();
	for( $i = 0; $i < 4; $i++ )
	{
		$small_story_args['is_first'] = $i == 0;
		$small_story_args['is_last']  = $i == 4 - 1;

		$story = new Control_small_story( array_shift( $business_layout->posts_stack ) );
		$story->display( $small_story_args );
	}
	?>
