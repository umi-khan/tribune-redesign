<?php
$taxonomy  = 'post_tag';
$field     = 'slug';
$term_slug = 'elections2013';

$term = get_term_by( $field, $term_slug, $taxonomy );
if(! $term ) return;

$args = array(
    'numberposts' => 13,
    'tax_query' => array(
        array(
            'taxonomy' => $taxonomy,
            'field'    => $field,
            'terms'    => $term_slug
        )
    )
);
$latest_stories = get_posts($args); 

$main_story_img_dimension = array(
	'width'  => 625,
	'height' => 469
);

$main_story               = LM_story::get_story( array_shift( $latest_stories ), $term->term_id, $term->term_id, $term->term_id );
$main_story_image_manager = new IM_Manager( $main_story->id, false );
$main_story_image         = $main_story_image_manager->default_image->large;

if( false === $main_story_image_manager->has_images() )
{
   $main_story_video_manager = new VM_Manager( $main_story->id, false );
   $main_story_video = $main_story_video_manager->default_video;
}?>

<h1><a href="<?php echo home_url('elections2013');?>/">Elections 2013: Live updates</a></h1>
<?php
if( isset( $main_story_video ) && false !== $main_story_video ) : 
   $main_story_video->player( $main_story_img_dimension['width'], $main_story_img_dimension['height']);
else :    
   ?>
<div id="picture-gallery">
   <div class="slideshow-container">
     <?php $main_story_img_dimension = $main_story_image->smart_dimensions( $main_story_img_dimension['width'], $main_story_img_dimension['height'] );?>
      <a class="image" href="<?php echo home_url('elections2013');?>/">
         <img src="<?php echo $main_story_image->url; ?>" alt="<?php echo $main_story_image->caption; ?>"
              width="<?php echo $main_story_img_dimension['width']; ?>" height="<?php echo $main_story_img_dimension['height']; ?>" />
      </a>
      <div class="caption">
         <div class="caption-text">
            <div class="caption-bg"></div>
               <h2><a href="<?php echo $main_story->permalink; ?>"><?php echo $main_story->title; ?></a></h2>
         </div>   
      </div>                       
   </div>   
</div>
<?php 
endif; ?>
<div class="long-stories carousel clearfix">
   <h4>
		<a href="<?php echo home_url('elections2013');?>/">Recent Update</a>
	</h4>
   <div class="carousel-pagination"></div>
   <div class="clear">
      <div class="items clearfix">
      <?php
      
      for( $i = 0, $j = count( $latest_stories ); $i < $j; $i++ ):   
         $args['is_first'] = FALSE;
         $args['is_last']  = FALSE; 
         $class = '';
         if( $i%4 == 0 )    {$class = ' first';$args['is_first'] = TRUE;}
         elseif($i%4 == 3 ) {$class = ' last';$args['is_last'] = TRUE;}         
            
         $story = LM_story::get_story( array_shift( $latest_stories ), $term->term_id, $term->term_id, $term->term_id );                                             
         //print'<pre>';print_r($story->post_details->ID);print'</pre>';         
         $sub_story = new Control_long_story( $story, false, true, true, false );?>
         <div class="item span-4<?php echo $class;?> elections2013-story">
         <?php $sub_story->display( $args );?>
         </div>   
      <?php
      endfor;?>
      </div>   
   </div>   
</div>