<?php

$sports_cat    = get_category_by_slug( 'sports' );
$sports_layout = new LM_layout( $sports_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 7 );

$sports_cat_link = get_category_link( $sports_cat->cat_ID );

?>

<h3 class="m-group">
	<a href="<?php echo $sports_cat_link; ?>"><?php echo $sports_cat->cat_name; ?></a>
</h3>
	<?php
	$small_story_args = array();
	for( $i = 0; $i < 4; $i++ )
	{
		$small_story_args['is_first'] = ( $i == 0  ? true : false );
		$small_story_args['is_last']  = ( $i == 4 - 1  ? true : false );

		$story = new Control_small_story( array_shift( $sports_layout->posts_stack ) );
		$story->display( $small_story_args );
	}
	?>
