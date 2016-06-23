<?php
/**
 * The template part for displaying content
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
			<span class="sticky-post"><?php _e( 'Featured', 'twentysixteen' ); ?></span>
		<?php endif; ?>

		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
	</header><!-- .entry-header -->

	<?php twentysixteen_excerpt(); ?>

	<?php 
	/*twentysixteen_post_thumbnail();*/



                                   if (empty($link)) {
                                       echo '<div class="featured-thumbnail">';
                                       the_post_thumbnail('mediumthumb', array('title' => ''));
                                       echo '</div>';
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

	<div class="entry-content">
		<?php
			/* translators: %s: Name of current post */
			the_content( sprintf(
				__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentysixteen' ),
				get_the_title()
			) );

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

	<footer class="entry-footer">
		<?php twentysixteen_entry_meta(); ?>
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
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
