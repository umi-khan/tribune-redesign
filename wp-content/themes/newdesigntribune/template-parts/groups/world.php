<?php

$world_cat    = get_category_by_slug( 'world' );
$world_layout = new LM_layout( $world_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 7 );

$world_cat_link = get_category_link( $world_cat->cat_ID );

?>

<h3 class="m-group"><a href="<?php echo $world_cat_link; ?>"><?php echo $world_cat->cat_name; ?></a></h3>

<div class="content">
	<div class="col-lg-6 first">
		<?php
		$story = new Control_top_story( array_shift( $world_layout->posts_stack ) );
		$story->display( array( 'is_first' => true, 'mob_story'=> true, 'style_bottom'=>true  ) );

		$story = new Control_story( array_shift( $world_layout->posts_stack ) );
		$story->display( array( 'is_last' => true ) );
		?>
	</div>

	<div class="col-lg-6 last">
		<?php
		$args = array();
		for( $i = 0; $i < 5; $i++ )
		{
			$args['is_first'] = ( $i == 0  ? true : false );
			$args['is_last']  = ( $i == 5 - 1  ? true : false );

			$story = new Control_small_story( array_shift( $world_layout->posts_stack ) );
			$story->display( $args );
		}
		?>
	</div>
</div>