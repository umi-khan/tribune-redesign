<?php /* Template Name: Home1 */

// current category
$category_id = 0;

$layout = new LM_layout( $category_id, LM_config::GROUP_MAIN_STORIES );

?>

<?php get_header();  ?>
<div class="home">
<div class="col-lg-8">
<?php
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
          </div>
<div class="group col-xs-12 col-lg-12">
<div style="width: 728px; margin:0 auto;">
<?php dynamic_sidebar( 'leaderboard-btf1' ); ?>
</div>
<div class="col-xs-12 col-lg-12 video clearfix group"><?php get_group( 'videos' ); ?></div>
<div class="group col-xs-12 col-lg-12">
<div style="width: 728px; margin:0 auto;">
<?php dynamic_sidebar( 'leaderboard-btf2' ); ?>
</div>
</div>
<div class="col-xs-12 col-lg-12 slideshow clearfix group"><?php get_group( 'slideshows', array( 'num_slideshows' => 4 ) ); ?></div>
  </div>
      <div class="col-lg-4 col-xs-12">
      <?php get_sidebar(); ?>
    </div>	
<div class="col-xs-12 col-lg-12" style="border-top: 1px solid #2a2a2a;     padding-top: 20px;">
<div class="col-xs-12 col-lg-3 clearfix group cat first"><?php get_group( 'pakistan' ); ?></div>
<div class="col-xs-12 col-lg-3 clearfix group cat"><?php get_group( 'sports' ); ?></div>
<div class="col-xs-12 col-lg-3 clearfix group cat"><?php get_group( 'business' ); ?></div>
<div class="col-xs-12 col-lg-3 clearfix group cat last"><?php get_group( 'life-style' ); ?></div>
</div>
</div>
</div>
<?php get_footer(); ?>
