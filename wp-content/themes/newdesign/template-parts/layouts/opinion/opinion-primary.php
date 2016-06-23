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
			$sub_stories[] = new Control_author_story( $sub_story, false, true );

		get_group( 'category-main-slideshow', array( 
			'top_story'           => $slideshowArray,
			'sub_stories'         => $sub_stories,
			'sub_stories_heading' => 'Latest Articles'
			) );

		// displaying editorial stories
		$editorial_cat    = get_category_by_slug( 'editorial' );
		$editorial_layout = new LM_layout( $editorial_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 4 );

		$stories = array();
		foreach( $editorial_layout->posts_stack as $editorial_story )
			$stories[] = new Control_long_story( $editorial_story, true );

		get_group( 'generic-four-column-wo-slider', array( 'category' => $editorial_cat, 'stories' => $stories ) );
	?>

	<?php
		// displaying more stories
		$contributors = new stdClass();
		$contributors->cat_name = 'Contributors';
		$contributors->slug     = 'contributors';
		$contributors->cat_ID   = false;

		$more_stories = $layout->template_stories[LM_config::TEMPLATE_MORE_STORIES];

		$stories = array();
		$stories['left_col']  = array();
		$stories['right_col'] = array();

		for( $i = 0, $j = count( $more_stories ); $i < $j; $i++ )
		{
			if( $i < 4 ) $stories['left_col'][]  = new Control_author_story( array_shift( $more_stories ) );
			else         $stories['right_col'][] = new Control_author_story( array_shift( $more_stories ) );
		}

		get_group( 'generic-two-column', array( 'category' => $contributors, 'stories' => $stories ) );
	?>

	<div class="slideshows group clearfix">
		<?php get_group( 'slideshows', array( 'heading' => 'Cartoons', 'num_slideshows' => 11 ) ); ?>
	</div>

	<?php
		// displaying letters
		$letters_cat    = get_category_by_slug( 'letters' );
		$letters_layout = new LM_layout( $letters_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 6 );

		$stories = array();
		
		$stories['left_col']   = array();
		$stories['left_col'][] = new Control_top_story( array_shift( $letters_layout->posts_stack ) );

		$stories['right_col'] = array();
		for( $i = 0, $j = count( $letters_layout->posts_stack ); $i < $j; $i++ )
			$stories['right_col'][] = new Control_text_story( array_shift( $letters_layout->posts_stack ) );

		get_group( 'generic-two-column', array( 'category' => $letters_cat, 'stories' => $stories, 'is_last' => true ) );
	?>
	
</div>
</div>