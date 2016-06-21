<?php
/**
 * The template part for displaying an Author biography
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

<div class="author-info">
	<div class="author-avatar">
		<?php
		/**
		 * Filter the Twenty Sixteen author bio avatar size.
		 *
		 * @since Twenty Sixteen 1.0
		 *
		 * @param int $size The avatar height and width size in pixels.
		 */
		//$author_bio_avatar_size = apply_filters( 'twentysixteen_author_bio_avatar_size', 42 );

		//echo get_avatar( get_the_author_meta( 'user_email' ), $author_bio_avatar_size );

		$post_authors = get_post();
		$authors_links = array();
		$post_authors->post_author ;
		foreach( (array)$post_authors->post_author as $a )
		{
			if( is_array( $post_authors->post_author ) && count( $post_authors->post_author ) == 1 &&
					  preg_match( "/\bexpress\b/i", $a->name ) ) continue;

		$authors_links[] = '<a href="'.$a->url.'" title="'.esc_attr( "Posts by " . $a->name ).'">'.$a->name.'</a>';
		}
		?>
	</div><!-- .author-avatar -->
	<div class="meta">
	<div class="author">By <a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo get_the_author(); ?></a></div>
	<div class="timestamp" title="<?php echo $story->date_gmt; ?>">Published: <?php echo the_date(); ?></div>
	</div>
	<!--<div class="author-description">
		<h2 class="author-title"><span class="author-heading"><?php _e( 'Author:', 'twentysixteen' ); ?></span> <?php echo get_the_author(); ?></h2>

		<p class="author-bio">
			<?php the_author_meta( 'description' ); ?>
			<a class="author-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
				<?php printf( __( 'View all posts by %s', 'twentysixteen' ), get_the_author() ); ?>
			</a>
		</p>
	</div><!-- .author-description -->
</div><!-- .author-info -->
