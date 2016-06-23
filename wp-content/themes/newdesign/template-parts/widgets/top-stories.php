<?php

// current category
$category_id = 0;

$layout = new LM_layout( $category_id, LM_config::GROUP_MAIN_STORIES );



        $main_story = new Control_main_story( array_shift( $layout->template_stories[LM_config::TEMPLATE_SUB_STORY] ) );
        $main_story->display( array( 'is_first' => true, 'mob_story'=> true ) );

        $args = array( 'is_first' => false, 'is_last' => false );
       // for( $i = 0, $j = count( $layout->template_stories[LM_config::TEMPLATE_SUB_STORY] ); $i < $j; $i++ )
        for( $i = 0, $j = 3; $i < $j; $i++ )
        {
          if( $i == $j - 1 ) $args['is_last'] = true;
          $sub_story = new Control_story( $layout->template_stories[LM_config::TEMPLATE_SUB_STORY][$i] );
          $sub_story->display( $args );
        }
?>

