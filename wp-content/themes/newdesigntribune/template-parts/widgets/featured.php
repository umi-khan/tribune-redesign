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
$featured_layout = new LM_layout( (int)$category_id, LM_config::GROUP_FEATURED_STORIES );

?>

<div class="featured-stories widget">
	<h4>Featured</h4>

	<?php
	for( $i = 0, $j = count( $featured_layout->posts_stack ); $i < $j; $i++ ) :
      
      if ( false === $is_middle_sidebar ) :
         $story  = $featured_layout->posts_stack[$i];
         $author = SM_author_manager::get_author_posts_link( $story->id );

         $html_classes = $story->html_classes;

         if( $i == 0 )      $html_classes .= " first";
         if( $i == $j - 1 ) $html_classes .= " last";?>
         <div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?>">
            <a class="title" href="<?php echo $story->permalink; ?>" title="<?php echo $story->tooltip; ?>">
               <?php echo $story->title; ?>
            </a>
            <div class="meta">
               <span class="author"><?php echo $author; ?></span>
            </div>
         </div>
   
   <?php else: ?>
   
         <div class="featured-story-container clearfix <?php if( $i == $j - 1 ) echo 'last'; ?>">
            <?php
               $story = new Control_small_picture_story( $featured_layout->posts_stack[$i], (int)$category_id, false, false, true );
               $story->display();
            ?>
         </div>
	<?php 
         endif;
   endfor; ?>
</div>