<?php
	if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

	global $post, $id, $withcomments, $wp_query, $slideshow;

//	$id = (int) get_query_var( 'post_id' );
   $id = (string) get_query_var( 'p' );
//var_dump($wp_query->query_vars);  
/*
$id=(string) get_query_var('name');
$args=array(
  'post_name' => $id,
  'post_type' => 'slideshow'
);
$my_query = get_post($story_id);
$id=$my_query->ID;

*/
	if( false == $id ) $slideshow = @array_pop( SS_manager::get_latest( 0, 0, 1 ) );
	else $slideshow   = new SS_slideshow( $id );

	if( function_exists( 'pp_record_hit' ) ) pp_record_hit( $id );


	$images        = $slideshow->images;
	$id            = $slideshow->id;
	$title         = $slideshow->title;
	$images_count  = count( $images );
	$permalink     = get_permalink( $id );
	$default_image = $slideshow->default_image;

	// setup the wordpress post object
	$post = get_post( $id );
	$wp_query->is_single = true;

	//exp_insert_css('slideshow');
	//exp_insert_script('slideshow');
	//exp_insert_script('single-story');
	//exp_insert_css('wmd');
	get_header();
?>

<div class="multimedia">
<div class="single">
   <div id="id-<?php echo $id; ?>" class="story clearfix">      
   <?php
   if ( !empty( $images ) ) :

      $large_image_width = 775;
      $image_dimensions  = $default_image->large->smart_dimensions( $large_image_width, round($large_image_width * 0.75) );

      $thumbnail_size_w = get_option('thumbnail_size_w');
      $thumbnail_size_h = get_option('thumbnail_size_h');
      $ratio = ( $thumbnail_size_w > 0 && $thumbnail_size_h > 0 ) ? $thumbnail_size_h / $thumbnail_size_w : 0.75;

      $border_width     = 1;
      $thumbnail_width  = 133;
      $thumbnail_height = round( $thumbnail_width * $ratio );
      $top_margin       = 0;
            
      $carousel_height  = ( ( $thumbnail_height + ( 2 * $border_width ) + $top_margin ) * $images_count );      
   ?>
      
      <div id="picture-gallery">                  
         
         <div class="col-lg-9 primary">                        
            <div class="slideshow-container">
               <h1 class="title">
                  <a href='<?php echo $permalink; ?>'><?php echo $title;?></a>         
               </h1>              
               <img src="<?php echo $default_image->large->url;?>" alt="<?php echo $default_image->caption;?>"
                    width="774" height="583"
                    class="" />
               <p class="caption"><?php echo $default_image->caption; ?></p>               
               <div class="nav">
                  <a title="Next Image" class="next-item disabled"></a>
                  <a title="Previous Image" class="prev-item disabled"></a>
               </div>
            </div>
         </div>
         <div class="col-lg-2 last seconday" style="float: right; width: 22%;">
            <div class="carousel-container">
               <a class="prev carousel-prev carousel-vertical-prev"></a>
               <div class="carousel carousel-vertical">               
                  <div class="items clearfix" style="width: auto !important; height: <?php echo $carousel_height;?>px;" >
                     <?php                      
                           foreach( $images as $image ) :
                              $class = $count++ == 0 ? 'first' : ($counter == $images_count ? 'last' : '');
                              if($image->id == $default_image->id) $class .= ' current';
                        ?>
                        <img src="<?php echo $image->thumbnail->url;?>"
                           alt="<?php echo $image->caption;?>"
                           longdesc="<?php echo $image->large->url; ?>"
                           class="item <?php echo $class;?>"
                           width="<?php echo $thumbnail_width;?>"
                           height="<?php echo $thumbnail_height;?>" />
                     <?php endforeach; ?>
                  </div>               
               </div>
               <a class="next carousel-next carousel-vertical-next"></a>
            </div>
         </div>
      </div>
      <p class="excerpt">
         <?php echo $slideshow->short_description; ?>
      </p>
      <div class="social-widget story-social-links clearfix">

         <div class="twitter-link">
            <a href="http://twitter.com/share" class="twitter-share-button"
               data-count="horizontal" data-url="<?php echo $permalink; ?>" data-via="etribune"
               data-text="<?php echo trim(esc_html($title)); ?>" >Tweet</a>
         </div>
         
         <div class="fb-like" data-href="<?php echo $permalink; ?>" data-send="false" data-layout="button_count" data-width="100" data-show-faces="true"></div>   
         <!--<fb:like colorscheme="evil" href="<?php echo $permalink; ?>" layout="button_count" show_faces="false" width="100px"></fb:like>-->                             
         
         <?php exp_single_comments_link( $id ,true, true );?>

         <a href="/email/<?php echo $id;?>/" title="Email story to friend" class="email-friend" target="_blank" >
            <img src="<?php bloginfo('template_url')?>/img/email-icon-btf.gif?v=1.0" alt="" />
            <span>Email a friend</span>
         </a>
      </div>
     <?php endif; ?>
   </div>

   <div class="primary col-lg-9" style="min-height: 500px; margin-left: -12px;"> 
<?php  comments_template('' ,true); ?>
   </div>

   <div class="col-lg-3 sidebar last">
      <?php exp_load_widget_file( "more-slideshows" ); ?>
      <?php // dynamic_sidebar('Slideshow Page Right Sidebar'); ?>
   </div>
   
</div>
</div>
<?php // exp_insert_script('fb-sdk'); ?>
<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/slideshow.js"></script>
</div>
<?php get_footer(); ?>