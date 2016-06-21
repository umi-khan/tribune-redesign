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
?>
<div class=" col-lg-12">
<article id="post-<?php the_ID(); ?>" <?php post_class(); $link = video_link(get_the_ID());?> >

	   <div id="id-<?php echo the_ID(); ?>" class="story clearfix">
      <h1 class="title"><a href="<?php echo $story->permalink; ?>"><?php echo the_title(); ?></a></h1>
      <div class="excerpt display-none">
         <?php echo the_excerpt(); ?>
      </div>
<?php 			
				get_template_part( 'template-parts/biography' );
			 ?>

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
	<?php //twentysixteen_excerpt(); ?>

	<?php 
	//twentysixteen_post_thumbnail(); ?>
	<?php

                                    if (empty($link)) {
                                        echo '<div class="featured-thumbnail">'; ?>
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

										</div>
                                    <?php    echo '</div>';
                                    } else {
                                        $video_link = explode("/", $link);
                                        $count = count($video_link);
                                        $video_link_ytube = explode("=", $link);
                                        if (strpos($link, 'vimeo.com')) {
                                            ?>
                                            <iframe width="100%" height="450" src="http://player.vimeo.com/video/<?php echo $video_link[3]; ?>"></iframe>
                                        <?php } elseif (strpos($link, 'facebook.com')) {
                                            ?>
                                            <iframe width="100%" height="450"  src="//www.facebook.com/video/embed?video_id=<?php echo $video_link[$count - 2]; ?>" frameborder="0" webkitallowfullscreen="" mozallowfullscreen="" allowfullscreen=""></iframe>
                                        <?php } elseif (strpos($link, 'dailymotion.com')) {
                                            ?>
                                            <iframe width="100%" height="450" src="//www.dailymotion.com/embed/video/<?php echo $video_link[5]; ?>" frameborder="0"></iframe>
                                        <?php } elseif (strpos($link, 'youtube.com')) {
                                            ?>
                                            <iframe id="ytplayer" type="text/html" width="100%" height="450" src="http://www.youtube.com/embed/<?php echo $video_link_ytube[1]; ?>" frameborder="0" ></iframe>
                                        <?php } elseif (strpos($link, 'jwplatform.com')) {
                                            ?>
                                            <iframe type="text/html" width="100%" height="450" src="http://content.jwplatform.com/players/<?php echo $video_link[4]; ?>" frameborder="0" ></iframe>
                                        <?php } ?>


                                    <?php } ?>


	<div class="entry-content story-content">


		<?php
			the_content();

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentysixteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'twentysixteen' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );

		?>
		
	</div><!-- .entry-content -->

<div class="entry-footer">
        <?php //twentysixteen_entry_meta();
            $tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'twentysixteen' ) );
            if ( $tags_list ) {
                printf( '<span class="trends-line"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                    _x( 'Tags', 'Used before tag names.', 'twentysixteen' ),
                    'Read more: '.$tags_list
                );
            }


         ?>
        <?php
            edit_post_link(
                sprintf(
                    /* translators: %s: Name of current post */
                    __( 'Edit<span class="screen-reader-text"> "%s"</span>', 'twentysixteen' ),
                    get_the_title()
                ),
                '<span class="edit-link">',
                '</span>'
            );
        ?>
    </div><!-- .entry-footer -->
</article><!-- #post-## -->
