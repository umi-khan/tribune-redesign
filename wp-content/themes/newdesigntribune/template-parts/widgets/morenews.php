<?php

$category_id       = 0;
$is_middle_sidebar = false;

if( false == is_home() )
{
   if( isset( $data ) && !empty( $data ) ) extract( $data );
   
   if( is_null( $category_id ) )
   {
      global $wp_query;
      $category = $wp_query->get_queried_object();

      $category_id = $category->cat_ID;
   }
}
$layout = new LM_layout( (int)$category_id, LM_config::GROUP_MAIN_STORIES );

?>
<div class="featured-stories widget">
   <h4>More News</h4>
   <?php 
      $args = array( 'is_first' => false, 'is_last' => false );
      for( $i = 0, $j = ((count( $layout->template_stories[LM_config::TEMPLATE_MORE_STORIES] )) <= 6 ) ? (count( $layout->template_stories[LM_config::TEMPLATE_MORE_STORIES] )) :6; $i < $j; $i++ )
         {
            $args['is_first'] = ( $i == 0 ) ? true : false;
            $args['is_last']  = ( $i == $j - 1 ) ? true : false;
            $more_story = new Control_more_story( $layout->template_stories[LM_config::TEMPLATE_MORE_STORIES][$i] );
            $more_story->display( $args );
         }
   ?>
   
</div>