<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>
<div class="row">
<div id="primary" class="content-area col-lg-8 story">
	<main id="main" class="site-main" role="main">
		<?php
		// Start the loop.
		while ( have_posts() ) : the_post();

			// Include the single post content template.
			get_template_part( 'template-parts/content', 'single' );



			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				dynamic_sidebar( 'comment-stories' );
			}

			// End of the loop.
		endwhile;
		?>

	</main><!-- .site-main -->

	

</div><!-- .content-area -->
<div class="col-lg-4">
<div class="col-lg-12 col-xs-12 col-sm-12">
<?php dynamic_sidebar('sidebar-3'); ?>
</div>
</div>
</div>
</div>
<?php get_footer(); ?>
