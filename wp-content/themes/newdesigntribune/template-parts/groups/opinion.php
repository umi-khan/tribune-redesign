<?php

$opinion_cat      = get_category_by_slug( 'opinion' );
$opinion_layout   = new LM_layout( $opinion_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 13 );
$opinion_cat_link = get_category_link( $opinion_cat->cat_ID );

?>

<h3 class="m-group">
	<a href="<?php echo $opinion_cat_link; ?>"><?php echo $opinion_cat->cat_name; ?></a>
</h3>

<div class="col-lg-6 first">
	<?php
		$top_story = new Control_top_story( array_shift( $opinion_layout->posts_stack ) );
		$top_story->display( array( 'is_first' => true, 'is_last' => true ) );
	?>

	<div class="editorial">
		<?php $editorial_category = get_category_by_slug( 'editorial' ); ?>
		<h4>
			<a href="<?php echo get_category_link( $editorial_category->cat_ID ); ?>" >
				<?php echo $editorial_category->cat_name; ?>
			</a>
		</h4>

		<?php
			$editorial_layout  = new LM_layout( $editorial_category->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 2 );
			$editorial_stories = $editorial_layout->posts_stack;
		?>
		<div class="clearfix">
			<?php for( $i = 0; $i < 2; $i++ ): ?>
			<div class="col-lg-6  <?php if( $i == 2 - 1 ){ echo "last";}else{echo "first";} ?>">
				<?php
				$small_pic_story = new Control_small_picture_story( $editorial_stories[$i], $editorial_category );
				$small_pic_story->display();
				?>
			</div>
			<?php endfor; ?>
		</div>
	</div>
</div>

<div class="col-lg-6 last contributors">
	<h4>
		<a href="<?php echo $opinion_cat_link; ?>">Contributors</a>
	</h4>

	<div class="carousel-pagination"></div>

	<div class="carousel clear">
		<div class="items clearfix">
			<?php
			$num_author_story_pages = ceil( 12 / 4 );
			for($i = 0; $i < $num_author_story_pages; $i++ ) :
			?>
			<div class="item">
				<?php
					$args = array( 'is_first' => false, 'is_last' => false );
					for($j = 0; $j < 4; $j++ )
					{
						$args['is_first'] = ( $j == 0 ) ? true : false;
						$args['is_last']  = ( $j == 4 - 1 ) ? true : false;

						$author_story = new Control_author_story( array_shift( $opinion_layout->posts_stack ) );
						$author_story->display( $args );
					}
				?>
			</div>
			<?php endfor; ?>
		</div>
	</div>
</div>