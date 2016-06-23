<?php get_header();
global $authordata , $paged, $wpdb;
$authordata  = get_userdata( get_query_var( 'author' ) );

$author_nicename    = explode('-', $authordata->user_nicename);
$author_name = $author_nicename[0].' '.$author_nicename[1];
$author_email   = $authordata->user_email;
$express_domains = array( 'tribune.com.pk', 'expressnews.tv', 'express.com.pk');
$author_url     = get_author_posts_url( $authordata->ID );

$current_letter = strtoupper( $author_name[0] );

$template_url = get_bloginfo( 'template_url', 'display' );
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
   'meta_query' => array(
    array(
      'meta_key' => '_sm_author',
      'meta_value' => get_query_var( 'author'))),
   	  'post_type' => 'post',
      'post_status' => 'publish',
      'paged' => $paged

);
$wp_query = new WP_Query( $args );

 ?>
<div id="authors" class="col-lg-8">

	<!-- Primary Column -->
	<div class="primary">

		<div id="author-navigation">
			<h1 class="title">Authors &rsaquo; <?php echo $current_letter; ?> &rsaquo; <?php echo $author_name; ?></h1>

			<ul class="pagination clearfix">
				<?php for( $i = 65; $i <= 90; $i++ ) : ?>
				<li>
					<a href="#" <?php echo $current_letter == chr( $i ) ? ' class="active"' : ''; ?>><?php echo chr( $i ); ?></a>
				</li>
				<?php endfor; ?>
			</ul>

			<img src="<?php bloginfo('template_url') ?>/img/spinner.gif" border="0" alt="" id="loadin_img" />


		</div>

		<div id="author-stories">

			<?php if ($wp_query->have_posts()) : ?>

			<h1 class="title">
				Stories by <?php echo $author_name; ?>
				<br/>
				<?php if ($author_email && in_array(substr($author_email, strrpos($author_email, '@')+1), $express_domains)) : ?>
					<strong><?php echo str_replace('@', ' (AT) ',$author_email)  ?></strong>
				<?php endif; ?>
			</h1>
			
			<?php while ($wp_query->have_posts()) : $wp_query->the_post(); 

					global $post;
					$story         = LM_Story::get_story( $post );
					$image_manager = new IM_Manager( $post->ID );
					$video   = false;
					if( false === $image_manager->has_images() )
					{
						$video_manager = new VM_Manager( $post->ID, false );
						$video         = $video_manager->default_video;
					}

					$default_image = ( isset( $video ) && false !== $video ) ? $video->thumbnail : $image_manager->default_image->thumbnail;
			?>

				<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?> couplet clearfix">
					<a class="image" href="<?php echo $story->permalink; ?>">
						<img src="<?php echo $default_image->url; ?>" alt="<?php $default_image->caption; ?>" width="120" height="90" />
					</a>

					<h2 class="title"><a href="<?php echo $story->permalink; ?>" ><?php echo $story->title; ?></a></h2>
					<p class="excerpt"><?php echo $story->excerpt;?></p>
					<div class="meta">
						<span class="timestamp" title="<?php echo $story->date_gmt;?>"><?php echo $story->date;?></span>
					</div>
				</div>
			<?php endwhile; ?>

				<?php endif; ?>
			
			<div class="pagination">
				<?php wp_pagenavi(); ?>
			</div>
		</div>
		
	</div>

	

</div>
<div class="col-lg-4">
<?php  dynamic_sidebar('sidebar-3'); ?>

</div>

<?php get_footer(); ?>
