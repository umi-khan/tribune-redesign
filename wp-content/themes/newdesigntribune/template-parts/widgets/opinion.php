<?php

$opinion_cat      = get_category_by_slug( 'opinion' );
$opinion_layout   = new LM_layout( $opinion_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 13 );
$opinion_cat_link = get_category_link( $opinion_cat->cat_ID );

?>

<h4 class="m-group">
	<span>Opinion & Editorial</span>
</h4>

<div class="col-lg-12 first">
<div class="opinion">
	<?php for( $i = 0; $i < 2; $i++ ){ ?>
		<div class="col-xs-6 col-lg-6 <?php if( $i == 2 - 1 ){ echo "last";}else{echo "first";} ?>">
	<?php 
		$top_story = new Control_opinion_story( array_shift( $opinion_layout->posts_stack ) );
		$top_story->display( array( 'is_first' => true, 'mob_story'=> true, 'style_bottom'=>true  ) ); ?>
		</div>
	<?php	} 	?>
</div>
	<div class="editorial">
		<?php 
			$editorial_category = get_category_by_slug( 'editorial' ); 
			$editorial_layout  = new LM_layout( $editorial_category->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 2 );
			$editorial_stories = $editorial_layout->posts_stack;
		?>
			<?php for( $i = 0; $i < 2; $i++ ): ?>
			<div class="col-lg-12 col-sm-6 first">
				<?php
				$small_pic_story = new Control_small_picture_story( $editorial_stories[$i], $editorial_category );
				$small_pic_story->display();
				?>
			</div>
			<?php endfor; ?>
	</div>
</div>