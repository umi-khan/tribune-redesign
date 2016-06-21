<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

// current category
global $wp_query;
$category_id = $wp_query->get_queried_object()->cat_ID;

$layout = new LM_layout( $category_id, LM_config::GROUP_MAIN_STORIES );

?>

<div class="primary">
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

		// displaying more news
		$more_news = new stdClass();
		$more_news->cat_name = "More News";
		$more_news->cat_ID   = false;

 		// displaying featured
		$featured_cat = new stdClass();
		$featured_cat->cat_name = 'Featured';
		$featured_cat->slug     = 'featured';
		$featured_cat->cat_ID   = false;

		$featured_layout   = new LM_layout( $category_id, LM_config::GROUP_FEATURED_STORIES );

		$stories = array();
		$stories['left_col']   = array();
		for( $i = 0; $i < 2; $i++ )
			$stories['left_col'][] = new Control_top_story( array_shift( $featured_layout->posts_stack ) );

		$stories['right_col'] = array();
		foreach( $featured_layout->posts_stack as $story )
			$stories['right_col'][] = new Control_small_story( $story );

		get_group( 'generic-two-column', array( 'category' => $featured_cat, 'stories' => $stories ) );

		$stories = array();
		foreach( $layout->template_stories[LM_config::TEMPLATE_MORE_STORIES] as $more_story )
			$stories[] = new Control_long_story( $more_story, true );

		get_group( 'generic-four-column-wo-slider', array( 'category' => $more_news, 'stories' => $stories ) );

	?>

	<div class="slideshows group clearfix last">
		<?php get_group( 'slideshows', array( 'num_slideshows' => 11 ) ); ?>
	</div>
</div>
</div>