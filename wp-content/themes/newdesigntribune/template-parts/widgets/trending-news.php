<?php

$category_id       = 0;
// Left for future enhancement if demanded!

// if( false == is_home() )
// {
//    if( isset( $data ) && !empty( $data ) ) extract( $data );
   
//    if( is_null( $category_id ) )
//    {
//       global $wp_query;
//       $category = $wp_query->get_queried_object();

//       $category_id = $category->cat_ID;
//    }
// }

$layout = new LM_layout( (int)$category_id, LM_config::GROUP_TRENDING_STORY );
?>
   <div class="more-story trending">
      <h4 class="top-news"><span>Trending</span></h4>
   <?php 
      $args = array( 'is_first' => false, 'is_last' => false,'is_big' => true );
      for( $i = 0, $j = 1; $i < $j; $i++ )
         {
            $args['is_first'] = ( $i == 0 ) ? true : false;
            $args['is_last']  = ( $i == $j - 1 ) ? true : false;
            $more_story = new Control_trend_pic_story( $layout->template_stories[LM_config::TEMPLATE_TRENDING_STORIES][$i] );
            $more_story->display( $args );
         }

//Small stories
            $args = array( 'is_first' => false, 'is_last' => false,'is_big' => true, 'off_meta' => true );
            for( $i = 1, $j = count( $layout->template_stories[LM_config::TEMPLATE_TRENDING_STORIES] ); $i < $j; $i++ )
            {
               if( $i == $j - 1 ) $args['is_last'] = true;
               $sub_story = new Control_trending_small_story( $layout->template_stories[LM_config::TEMPLATE_TRENDING_STORIES][$i] );
               $sub_story->display( $args );
            }
   ?>
  
</div> 

 


 