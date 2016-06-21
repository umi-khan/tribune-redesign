<?php
/**
 * The template part for displaying single posts
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
//$default_image = get_post();
global $post;
$story = LM_Story::get_story($post);
$image_manager = new IM_Manager($story->id);  //fetched prior to wp_head() action so taht function hooking to wp_head() can get the global story Images
$default_image = $image_manager->default_image;
$images_count = count($image_manager->images);
if ($images_count == 0) {
   $video_manager = new VM_Manager($story->id);
   $default_video = $video_manager->default_video;
}
// get the story author
$author = SM_author_manager::get_author_posts_link($story->id);
$limit_of_trends = 3;
$trends = $story->trends;
$is_magazineFeatured = false;
if (class_exists('SM_magazineFeatured_admin')) $is_magazineFeatured = ( get_post_meta($story->id, SM_magazineFeatured_admin::META_KEY, true) );

// For Chartbeat - make $author and $cat_name global, so that they
// can be accessed in footer.php - as input to Chartbeat's script
global $author_name;
global $cat_name;
preg_match("/<a[^>]*>(.*?)<\/a>/", $author, $matches);
$author_name = $matches[1];

$category = get_the_category($story->id);
$cat_name = $category[0]->cat_name;
?>
<div class=" col-lg-12">
<article id="post-<?php the_ID(); ?>" >
<div class="col-lg-12"><h1 class="title top-heading"><a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a></h1></div>
<div class="col-lg-12 stickybar"><div class="col-lg-8 last stickytitle"><h1 class="title"><span class="slogo"><a href="/" title="Tribune" style="background-image:url('<?php echo get_template_directory_uri().'/img/t.gif';?>');">&nbsp;</a></span> <?php echo $story->title; ?></h1>
</div><div class="col-lg-3 last stickyicons"><a href="#" class="ss-button facebook like-button share s_facebook"><span class="fb"></span>Share</a>
  <a href="#" class="ss-button twitter like-button share s_twitter"><span class="tw"></span>Tweet <!-- <span class="like-counter counter c_twitter"></span> --></a>
</div></div>
   <?php if ($is_specialFeatured) : ?>
      <div class="single primary span-24" style="border-right:none;">
   <?php else :
    ?>
      <div class="single primary span-16">
   <?php endif; ?>
   <div id="id-<?php echo $story->id; ?>" class="story clearfix">
      
      <div class="excerpt display-none">
         <?php echo $story->excerpt; ?>
      </div>
     

    <?php //Sponsor Story Integration
    if ( (class_exists('SM_special_featured_admin') ) && ( function_exists( 'run_sponsored_stories' ) ) ) {
       $et_sponsored_id = ( get_post_meta($story->id, SM_special_featured_admin::META_KEY_SPONSORED, true) ) ? ( get_post_meta($story->id, SM_special_featured_admin::META_KEY_SPONSORED, true) ) : 0;
  
      if( $et_sponsored_id != 0 ) { 
      $sp_byline      = get_post_meta($et_sponsored_id, '_ss_sectitle', true);
      $sp_link        = get_post_meta($et_sponsored_id, '_ss_link', true);
      $sp_title       = get_the_title($et_sponsored_id) ;
      $sp_image_array = wp_get_attachment_image_src(get_post_thumbnail_id($et_sponsored_id), 'full', true);
      $sp_image       = $sp_image_array[0];

      ?>
    <!-- Sponsor Story -->
     <div class="meta partner-ad">
       <div style="float: left; width: 74px; height: 74px"><img src="<?php echo $sp_image; ?>" width="74" height="74"></div>
    <div style="margin-left: 10px; float: left;">
      <a href="<?php echo $sp_link; ?>" style="font: bold 18px arial, sans-serif; color: #0076ee"><?php echo $sp_title; ?></a><br>
      <span style="font: normal 13px arial, sans-serif; color: #666"><?php echo $sp_byline; ?></span>
  </div>
      </div>
    <!-- /Partner Story -->
     <?php }} ?>

     <?php 
        $term_tia = "tech-in-asia";
        if (has_tag($term_tia)):
        $techinaisa = get_term_by('slug', $term_tia, 'post_tag');
        $techinaisa_link = get_term_link( $techinaisa->slug , 'post_tag'); ?>

    <!-- Tech In Asia -->
     <div class="meta partner-ad">
       <div style="float: left; width: 74px; height: 74px"><img src="/wp-content/themes/express/img/tia-logo.jpg" width="74" height="74"></div>
          <div style="margin-left: 10px; float: left;">
            <span style="font: bold 18px arial, sans-serif; color: #0076ee"><a href="<?php echo esc_url( $techinaisa_link ) ; ?>"><?php echo $techinaisa->name;?></a></span><br>
            <span style="font: normal 13px arial, sans-serif; color: #666">Publishing Partner</span>
        </div>
      </div>
    <!-- /Tech In Asia -->
    <?php endif; ?>


      <div class="clearfix story-content read-full">


            <div class="story-image" >
               <div class="story-image-container <?php echo ($is_specialFeatured) ? "full-page":""; ?>">
                  <?php if ($is_specialFeatured){ ?>

                  <img src="<?php echo $default_image->full->url;?>" alt="<?php echo $default_image->caption; ?>" width="<?php echo $image_dimensions['width']; ?>" height="<?php echo $image_dimensions['height']; ?>" />

                  <?php } else {?>
                    <?php if (has_post_thumbnail()): the_post_thumbnail( array(625, 469) ); else: ?>

                  <img src="<?php echo $default_image->large->url; ?>" alt="<?php echo $default_image->caption; ?>" width="<?php echo $image_dimensions['width']; ?>" height="<?php echo $image_dimensions['height']; ?>" />

                    <?php endif; ?>

                  <?php }?>
          
               </div>
               <p class="caption" <?php echo ($is_specialFeatured) ? "style='text-align:center;'": ""?>>
                  <?php
                  if (has_post_thumbnail()):
                    $args = array( 'post_type' => 'attachment', 'orderby' => 'menu_order', 'order' => 'ASC', 'post_mime_type' => 'image', 'post_status' => null, 'post_parent' => $post->ID );
                     $posts_array = get_posts( $args ); 
                    echo $posts_array[0]->post_excerpt;
                  else:
                    echo $default_image->caption;
                  endif;
                  ?>
               </p>
                
            </div>

      <div class="col-lg-2">
       <div class="meta">
         <?php if ($is_magazineFeatured) : ?>
            <div class="sunday-featured">
               Sunday Magazine Feature
            </div>
         <?php endif; ?>

            <div class="author"><span>By <?php echo $author; ?></span></div>

         <div class="timestamp" title="<?php echo $story->date_gmt; ?>"><?php echo $story->date; ?></div>
      </div>     
      <?php if (!$is_print) : ?>
            <!-- social start here -->
      <div id="socialshare">
        <div class="tshare"><span class="like-counter counter c_total total number">0</span><span class="text">SHARES</span></div>
        <a href="#" class="ss-button facebook like-button share s_facebook"><span class="fb"></span>Share</a>
        <a href="#" class="ss-button twitter like-button share s_twitter"><span class="tw"></span>Tweet <!-- <span class="like-counter counter c_twitter"></span> --></a>

       </div>
       <script type="text/javascript">
          var shareTitle = "<?php echo addslashes(html_entity_decode(get_the_title($story->id),ENT_QUOTES,'UTF-8'));?>";
          var shareText = "<?php echo addslashes(get_the_excerpt($story->id));?>";
          var shareImage = "<?php echo ($images_count != 0) ? $default_image->full->url:"";?> ";
          var shareUrl = "<?php echo $story->permalink;?>";
      </script>
            <?php endif; ?> 
      <!-- social end here -->
    </div>
         <?php
         if ($images_count > 1) :
            ?>
            <div class="carousel story-carousel">
               <div class="items clearfix">
                  <?php
                  $images = $image_manager->images;
                  for ($i = 0, $count = count($images); $i < $count; $i++) :
                     $image = $images[$i];
                     $size = $image->large->smart_dimensions(625, 470);
                     $class = '';
                     if ($i == 0)
                        $class .= ' first';
                     else if ($i == $count - 1)
                        $class .= ' last';
                     if ($image->id == $default_image->id)
                        $class .= ' current';
                     ?>
                     <img src="<?php echo $image->thumbnail->url; ?>" alt="<?php echo $image->caption; ?>"
                          width="80" height="60" class="item <?php echo $class; ?>"
                          longdesc="<?php echo $image->large->url; ?>"
                          largewidth="<?php echo $size['width']; ?>" largeheight="<?php echo $size['height']; ?>" />
                       <?php endfor; ?>
               </div>
            </div>
         <?php endif; ?>
         <div class="col-lg-10">
         <?php echo $story->content; ?>
         </div>
         <div>
         </div>     
         <?php
         if (!empty($trends)) :
            $trends = array_slice($trends, 0, $limit_of_trends); // there will never be more than 3
            ?>
            <div class="trends-line">
               Read more:
               <?php
               $count = 0;
               foreach ($trends as $trend) :
                  if ($count > 0) :
                     ?>, <?php endif; ?>
                  <a href="/<?php echo $trend->slug; ?>" ><?php echo $trend->name; ?></a>
                  <?php
                  $count++;
               endforeach;
               ?>
            </div>
      <?php endif; ?>
      
      <!-- Start Tribune EOA native: -->
      <div class="an-container" id="ZuDzVEFofJ96owBynbD5TXjZMBg4BKyzl2cZMTvQ"></div>
      <!-- End Tribune EOA native: -->

      <button class="read-full-story-button">Read full story</button>

      </div>
      


      <?php if( !is_single( 434529 ) && !is_single(301161) ): // don't show ads on story 434529 or 301161?>
      <div class="custom-ad" style="width: 300px; margin: 0 auto;">
          <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
          <!-- Tribune_Story_Inline_300x250_New -->
          <ins class="adsbygoogle"
               style="display:inline-block;width:300px;height:250px"
               data-ad-client="ca-pub-2620341023138785"
               data-ad-slot="3817324312"></ins>
          <script>
          (adsbygoogle = window.adsbygoogle || []).push({});
          </script>
      </div>
      <?php endif; ?>

<?php if (!$is_print) { ?>

         <div class="social-widget story-social-links clearfix">
 
 
 
         </div>
      <?php } ?>



<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>

   </div>
   <!-- div class="ad-fullbanner-btf-container clear">
      <div id="ad-fullbanner-btf" class="ad-fullbanner-btf"></div>
   </div -->

<!-- A/B Testing between Content.ad and Taboola -->
<?php if ($story->id % 2 == 0): // Show Content.ad if story id is even ?>
<div id="contentad104606"></div>
<script type="text/javascript">
    (function(d) {
        var params =
        {
            id: "5060b9a9-1acb-4dec-bac9-902f1105d069",
            d:  "dHJpYnVuZS5jb20ucGs=",
            wid: "104606",
            cb: (new Date()).getTime()
        };

        var qs=[];
        for(var key in params) qs.push(key+'='+encodeURIComponent(params[key]));
        var s = d.createElement('script');s.type='text/javascript';s.async=true;
        var p = 'https:' == document.location.protocol ? 'https' : 'http';
        s.src = p + "://api.content.ad/Scripts/widget2.aspx?" + qs.join('&');
        d.getElementById("contentad104606").appendChild(s);
    })(document);
</script>

<?php else: // Show Taboola when story id is odd ?>

<!-- Taboola -->
   <div id="taboola-below-article-thumbnails"></div>
<script type="text/javascript">
  window._taboola = window._taboola || [];
  _taboola.push({
    mode: 'thumbnails-a',
    container: 'taboola-below-article-thumbnails',
    placement: 'Below Article Thumbnails',
    target_type: 'mix'
  });
</script>
<!-- Taboola -->
<?php endif; ?>

<?php// comments_template('', true); ?>
   <script>
   
   </script>
</div>


<?php next_post_link('<div id="adjacent" class="span-9"><p>More in ' . $cat_name . '</p>%link</div>', '%title', true); ?>
</article><!-- #post-## -->
