<?php
/**
 * Template Name: RSS Page
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>
<div class="col-lg-8">
<div id="rss_page" class="span-24">

	<h1 class="page_title">RSS Feeds</h1>

	<!-- Primary -->
	<div class="span-16 primary">
		<p class="rss_intro">
		The Express Tribune offers RSS (Really Simple Syndication) feeds for its readers. Subscribe to any of our feeds to
		get the latest headlines, summaries and content, updated throughout the day.
		</p>

		<ul class="rss_categories">
			<li><a href="<?php bloginfo('rss2_url'); ?>">Home Page</a></li>

			<?php
				$categories = get_categories( array( 
					'orderby' => 'id', 'order' => 'ASC', 'parent' => 0, 'hide_empty' => 1, 'exclude' => 33
					) );
				foreach( ( array )$categories as $cat ):
			?>
			<li class="clearfix">
					<a href="<?php echo get_category_feed_link( $cat->cat_ID ); ?>"><?php echo $cat->name; ?></a>
					<?php
						$sub_categories = get_categories( array( 
							'orderby' => 'id', 'order' => 'ASC', 'parent' => $cat->cat_ID, 'hide_empty' => 1
							) );

						if( count( $sub_categories ) > 0 ) :
					?>

						<ul class="rss_subcategories">

						<?php foreach( (array)$sub_categories as $sub_cat ): ?>
							<li>
								<a href="<?php echo get_category_feed_link( $sub_cat->cat_ID ); ?>"><?php echo $sub_cat->name; ?></a>
							</li>
						<?php endforeach; ?>

						</ul>

					<?php endif; ?>
				</li>				
			<?php endforeach; ?>
			<li class="clearfix"><a href="http://blogs.tribune.com.pk/feed/">Blogs</a>
				<ul class="rss_subcategories">
					<li class="first"><a href="http://blogs.tribune.com.pk/videoscope/feed/">The Videoscope</a></li>
					<li><a href="http://blogs.tribune.com.pk/pakistan/feed/">Welcome To Pakistan</a></li>
					<li><a href="http://blogs.tribune.com.pk/opinion/feed/">The Verdict</a></li>
					<li><a href="http://blogs.tribune.com.pk/sports/feed/">Match Point</a></li>
					<li><a href="http://blogs.tribune.com.pk/style/feed/">The Good Life</a></li>
					<li><a href="http://blogs.tribune.com.pk/media/feed/">Media Watchdog</a></li>
					<li><a href="http://blogs.tribune.com.pk/society/feed/">The Way I See It</a></li>
					<li class="last"><a href="http://blogs.tribune.com.pk/world/feed/">The Big Picture</a></li>
				</ul>
			</li>
			<li class="clearfix"><a href="http://<?php echo $_SERVER["HTTP_HOST"]; ?>/multimedia/slideshows/feed/">Slideshows</a></li>
			<li class="clearfix last"><a href="http://<?php echo $_SERVER["HTTP_HOST"]; ?>/multimedia/videos/feed/">Videos</a></li>
		</ul>
	</div>
	<!-- Primary -->
</div>
</div>
<div class="col-lg-4">
<?php dynamic_sidebar('sidebar-5'); ?>

</div>
</div>
<?php get_footer(); ?>
