<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

// current category
global $wp_query;
$category_id = $wp_query->get_queried_object()->cat_ID;

$layout = new LM_layout( $category_id, LM_config::GROUP_MAIN_STORIES );

?>

<div class="span-16 primary">
	<?php
		// Make data array for slideshow
	foreach( $layout->template_stories[LM_config::TEMPLATE_TOP_STORY] as $pic_story ){
			$pic_story = new Control_picture_story( $pic_story );
			$slideshow_content = $pic_story->display($args = array(), 1);
				if( is_array( $slideshow_content ) ) extract( $slideshow_content );
					$slide_image_src= get_slide_image($story->id);
					$slideshowArray[0] = array(
						'link' =>  $story->permalink,
						'title' => $story->title,
						'img' => $slide_image_src['src'],
						'video' => $slide_image_src['isvideo']
					 );
		}

		for( $i = 0, $j = count( $layout->template_stories[LM_config::TEMPLATE_MORE_STORIES] ); $i < $j; $i++ ){
			$more_story = new Control_picture_story( $layout->template_stories[LM_config::TEMPLATE_MORE_STORIES][$i] );
				$slideshow_content = $more_story->display($args = array(), 1);
				if( is_array( $slideshow_content ) ) extract( $slideshow_content );
					$slide_image_src= get_slide_image($story->id);
					$slideshowArray[$i+1] = array(
						'link' =>  $story->permalink,
						'title' => $story->title,
						'img' => $slide_image_src['src'],
						'video' => $slide_image_src['isvideo']
					 );
			}
 			// End data array for slideshow
			
		// displaying main stories
		$top_story = array();
		foreach( $layout->template_stories[LM_config::TEMPLATE_TOP_STORY] as $top_story )
			$top_story = new Control_picture_story( $top_story );

		$sub_stories = array();
		foreach( $layout->template_stories[LM_config::TEMPLATE_SUB_STORY] as $sub_story )
			$sub_stories[] = new Control_small_story( $sub_story );

		get_group( 'category-main-slideshow', array( 'top_story' => $slideshowArray, 'sub_stories' => $sub_stories ) );
	?>

	<div class="slideshows group clearfix">
		<?php get_group( 'slideshows', array( 'num_slideshows' => 11 ) ); ?>
	</div>
	
	<?php
		// displaying more news
		$more_news = new stdClass();
		$more_news->cat_name = "More News";
		$more_news->cat_ID   = false;

 		// displaying music category
		$music_cat    = get_category_by_slug( 'music' );
		$music_layout = new LM_layout( $music_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 4 );

		$stories = array();
		$stories['left_col']   = array();
		$stories['left_col'][] = new Control_top_story( array_shift( $music_layout->posts_stack ) );

		$stories['right_col'] = array();
		foreach( $music_layout->posts_stack as $story )
			$stories['right_col'][] = new Control_small_story( $story );

		get_group( 'generic-two-column', array( 'category' => $music_cat, 'stories' => $stories ) );

		// displaying fashion category
		$fashion_cat    = get_category_by_slug( 'fashion' );
		$fashion_layout = new LM_layout( $fashion_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 4 );

		$stories = array();
		foreach( $fashion_layout->posts_stack as $story ) $stories[] = new Control_long_story( $story, true );

		get_group( 'generic-four-column-wo-slider', array( 'category' => $fashion_cat, 'stories' => $stories ) );
		
		// displaying tv & film category
		$tv_film_cat    = get_category_by_slug( 'tv-film' );
		$tv_film_layout = new LM_layout( $tv_film_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 4 );

		$stories = array();
		$stories['left_col']   = array();
		$stories['left_col'][] = new Control_top_story( array_shift( $tv_film_layout->posts_stack ) );

		$stories['right_col'] = array();
		foreach( $tv_film_layout->posts_stack as $story )
			$stories['right_col'][] = new Control_small_story( $story );

		get_group( 'generic-two-column', array( 'category' => $tv_film_cat, 'stories' => $stories ) );

		// displaying gossip category
		$gossip_cat    = get_category_by_slug( 'gossip' );
		$gossip_layout = new LM_layout( $gossip_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 4 );

		$stories = array();
		foreach( $gossip_layout->posts_stack as $story ) $stories[] = new Control_long_story( $story, true );

		get_group( 'generic-four-column-wo-slider', array( 'category' => $gossip_cat, 'stories' => $stories ) );

		// displaying food and books category
		$food_cat    = get_category_by_slug( 'food' );
		$food_layout = new LM_layout( $food_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 1 );

		$books_cat    = get_category_by_slug( 'books' );
		$books_layout = new LM_layout( $books_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 1 );

		$stories = array();
		$stories['left_col']   = array();
		$stories['left_col'][] = new Control_category_story( array_shift( $food_layout->posts_stack ), $food_cat );

		$stories['right_col']   = array();
		$stories['right_col'][] = new Control_category_story( array_shift( $books_layout->posts_stack ), $books_cat );

		get_group( 'generic-two-column', array( 'category' => false, 'stories' => $stories, 'is_last' => true ) );

		$stories = array();
		foreach( $layout->template_stories[LM_config::TEMPLATE_MORE_STORIES] as $more_story )
			$stories[] = new Control_long_story( $more_story, true );

		get_group( 'generic-four-column-wo-slider', array( 'category' => $more_news, 'stories' => $stories ) );

	?>
</div>
</div>