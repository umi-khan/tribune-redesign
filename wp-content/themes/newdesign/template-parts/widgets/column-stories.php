<?php
global $LAYOUT_MANAGEMENT;
$lm_featured_stories_group = $LAYOUT_MANAGEMENT->layout_groups[LM_config::GROUP_FEATURED_STORIES];
?>

<div class="featured-stories widget">
	<h4>Featured</h4>

	<?php
		for( $i = 0, $j = count( $lm_featured_stories_group->posts_stack ); $i < $j; $i++ ):
			$story  = $lm_featured_stories_group->posts_stack[$i];
			$author = SM_author_manager::get_author_posts_link( $story->id );

			$html_classes = $story->html_classes;

			if( $i == 0 )      $html_classes .= " first";
			if( $i == $j - 1 ) $html_classes .= " last";
	?>
			<div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?>">
				<a class="title" href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a>
				<div class="meta">
					<span class="author"><?php echo $author; ?></span>
				</div>
			</div>
	<?php endfor; ?>
</div>