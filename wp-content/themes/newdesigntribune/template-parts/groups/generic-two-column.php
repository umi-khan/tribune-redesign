<?php
	if( false == is_array( $stories ) || count( $stories ) < 1 ) return;

	if( $category )
		$category_link = ( $category->cat_ID === false ) ? false : get_category_link( $category->cat_ID );
?>

<div class="group <?php echo $category->slug; ?> clearfix <?php if( $is_last ) echo 'last'; ?>">
	<?php if( $category ) : ?>
	<h3>
	<?php if( $category_link ) : ?>
		<a href="<?php echo $category_link; ?>" ><?php echo $category->cat_name; ?></a>
	<?php else: ?>
		<?php echo $category->cat_name; ?>
	<?php endif; ?>
	</h3>
	<?php endif; ?>
	
	<div class="col-lg-6 first">
		<?php
			$args = array();

			for( $i = 0, $j = count( $stories['left_col'] ); $i < $j; $i++ )
			{
				$args['is_first'] = ( $i == 0  ? true : false );
				$args['is_last']  = ( $i == $j - 1  ? true : false );

				$story = $stories['left_col'][$i];
				
				if( $story instanceof IDisplayable_control ) $story->display( $args );
			}
		?>
	</div>

	<div class="col-lg-6 last">
		<?php
			for( $i = 0, $j = count( $stories['right_col'] ); $i < $j; $i++ )
			{
				$args = array();
				$args['is_first'] = ( $i == 0  ? true : false );
				$args['is_last']  = ( $i == $j - 1  ? true : false );

				$story = $stories['right_col'][$i];

				if( $story instanceof IDisplayable_control ) $story->display( $args );
			}
		?>

</div>
</div>