<?php

// current category
$category_id = 0;

$layout = new LM_layout( $category_id, LM_config::GROUP_MAIN_STORIES );



        $featured_layout = new LM_layout( (int)$category_id, LM_config::GROUP_FEATURED_STORIES );
        $args = array( 'is_first' => false, 'is_last' => false, 'is_end'=>false, 'is_mid' => false);
        for( $i = 0, $j = 5; $i < $j; $i++ ) {
        //for( $i = 0, $j = ((count( $featured_layout->posts_stack )) <= 10 )? count( $featured_layout->posts_stack ) : 10 ; $i < $j; $i++ ) {
          $args['is_first'] = ( $i == 0 ) ? true : false;
          // $args['is_last']  = ( $i == $j - 1 ) ? true : false;
          $args['is_mid']  = ($i == 5 || $i ==0 ) ?  true : false;
          
          if ($i > 4 ){
            $args['is_last'] = ($i % 2 != 0) ? true : false;  
          }else{
            $args['is_last'] = ($i % 2 == 0) ? true : false;
          }
          
          $args['is_end']  = ( $i == $j - 1 ) ? true : false;

                 $featured_story = new Control_long_pic_story( $featured_layout->posts_stack[$i], (int)$category_id, false, false, true );
                 $featured_story->display($args);
        }
?>

