<div class="group <?php echo $category->slug; ?> clearfix">
	<h3>
		<a href="<?php echo get_category_link( $category->cat_ID ) ; ?>" >
			<?php echo $category->cat_name; ?>
		</a>
	</h3>
	
	<div class="span-8">
		<?php
			$pic_story = array_shift( $stories );
			if( $pic_story instanceof IDisplayable_control )
				$pic_story->display( array( 'show_excerpt' => false, 'show_meta' => false ) );
		?>
	</div>
	
	<div class="span-4">
		<?php
			$story = array_shift( $stories );
			if( $story instanceof IDisplayable_control )
				$story->display( array( 'show_excerpt' => true, 'show_meta' => true ) );
		?>
	</div>
	
	<div class="span-4 last">
		<?php
			$story = array_shift( $stories );
			if( $story instanceof IDisplayable_control )
				$story->display( array( 'show_excerpt' => true, 'show_meta' => true ) );
		?>
	</div>
</div>