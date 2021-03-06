<?php
	if( false == is_array( $stories ) || count( $stories ) < 1 ) return;

	$category_link = ( $category->cat_ID === false ) ? false : get_category_link( $category->cat_ID );
?>

<div class="group carousel long-stories <?php echo $category->slug; ?> clearfix">
	<h3>
	<?php if( $category_link ) : ?>
		<a href="<?php echo $category_link; ?>" ><?php echo $category->cat_name; ?></a>
	<?php else: ?>
		<?php echo $category->cat_name; ?>
	<?php endif; ?>
	</h3>

	<div class="carousel-pagination"></div>

	<div class="clear">
		<div class="items clearfix">
		<?php
			$long_story_args = array();

			for( $i = 0, $j = count( $stories ); $i < $j; $i++ ) :
				if( $i == 0 )             $class = 'first';
				if( ( $i + 1 ) % 4 == 0 ) $class = 'last';
				else                      $class = '';

				if( $stories[$i] instanceof IDisplayable_control ) :
		?>
				<div class="item col-lg-3 <?php echo $class;?>">
					<?php $stories[$i]->display( $long_story_args ); ?>
				</div>
		<?php
				endif;
			endfor;
		?>
		</div>
	</div>
</div>