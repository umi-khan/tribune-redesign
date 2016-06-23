<?php

if( isset( $data ) && !empty( $data ) ) extract( $data );

$category = get_category( $category_id );
$layout   = new LM_layout( $category_id, LM_config::GROUP_FEATURED_STORIES, true, 3 );

foreach ( (array)$layout->posts_stack as $story ) :
	$story = new Control_small_picture_story( $story, $category );
?>
	<div class="<?php echo $category->slug ?>">
		<?php $story->display(); ?>
	</div>
		
<?php endforeach; ?>