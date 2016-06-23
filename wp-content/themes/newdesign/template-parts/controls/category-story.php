<div class="<?php echo $category->slug; ?> clearfix">
	<h3>
		<a href="<?php echo get_category_link( $category->cat_ID ); ?>"><?php echo $category->cat_name; ?></a>
	</h3>

	<?php include( trailingslashit( dirname( __FILE__ ) ) . 'story.php' ); ?>
</div>