<?php
/**
 * RSS2 Feed Template for displaying RSS2 Posts feed for authors.
 */

$authordata  = get_userdata( get_query_var( 'author' ) );
$author_name = ucwords( trim( $authordata->first_name . ' ' . $authordata->last_name ) );

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	xmlns:media="http://search.yahoo.com/mrss/"
	<?php do_action('rss2_ns'); ?>
>

<channel>
	<title><?php bloginfo_rss('name'); ?> &#187; <?php echo $author_name; ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url') ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php echo get_option('rss_language'); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action('rss2_head'); ?>

	<?php 
		while( have_posts()) :
			the_post();
			$story            = LM_story::get_story( get_the_ID() );
			$image_manager    = new IM_Manager( $story->id, false );
			$default_image    = $image_manager->default_image;

			$image_title = $image_caption = '';
			$image_dimensions = array(
				'width'  => 424,
				'height' => 318
			);
			$default_video = false;

			if( false === $image_manager->has_images() )
			{
				$video_manager = new VM_Manager( $story->id, false );
				$default_video = $video_manager->default_video;
			}

			if( false === $default_video )
			{
				$image_title      = $default_image->title;
				$thumbnail_url    = $default_image->thumbnail->url;
				$image_caption    = $default_image->caption;
				$image_dimensions = $default_image->large->smart_dimensions( $image_dimensions['width'], $image_dimensions['height'] );
				$image_large_url  = $default_image->large->url;
			}
			else
			{
				$thumbnail_url   = $default_video->thumbnail->url;
				$image_large_url = substr( $thumbnail_url, 0, strrpos( $thumbnail_url, '/' ) + 1 ) . '0.jpg';
			}
	?>
	<item>
		<title><?php echo $story->title; ?></title>
		<link><?php echo $story->permalink; ?></link>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true, $story->post_details), false); ?></pubDate>

		<guid isPermaLink="false"><?php echo $story->post_details->guid; ?></guid>

		<description>
		<![CDATA[
			<a href="<?php echo $story->permalink; ?>">
				<img src="<?php echo $thumbnail_url; ?>" width="160" height="120" alt="" />
			</a>
			<p><?php echo $story->content; ?></p>
			<br clear="all"/>
		]]>
		</description>

		<media:content width="<?php echo $image_dimensions['width']; ?>" height="<?php echo $image_dimensions['height']; ?>"
							isDefault="true" medium="image" url="<?php echo $image_large_url; ?>">
			<media:title><?php echo $image_title; ?></media:title>
			<media:description><?php echo $image_caption; ?></media:description>
			<media:thumbnail url="<?php echo $thumbnail_url; ?>" width="160" height="120" />
      </media:content>

		<wfw:commentRss><?php echo esc_url( get_post_comments_feed_link( $story->id, 'rss2' ) ); ?></wfw:commentRss>
		<slash:comments><?php echo get_comments_number( $story->id ); ?></slash:comments>
	</item>
	<?php endwhile; ?>

</channel>
</rss>