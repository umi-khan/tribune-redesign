<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

get_header();
?>

<div class="archive primary span-16">
	<?php
		for( $i = 0, $j = count($posts); $i < $j; $i++ ) :
			$story   = LM_Story::get_story( $posts[$i] );
			$img_mgr = new IM_Manager( $story->id );
			$def_img = $img_mgr->default_image->thumbnail;

			$story_classes = $story->html_classes . ( ( $i == $j - 1 ) ? ' last' : '' );
			$author  = SM_author_manager::get_author_posts_link( $story->id );
	?>

		<div id="<?php echo $story->html_id; ?>" class="<?php echo $story_classes; ?> couplet clearfix">
			<a class="image" href="<?php echo $story->permalink; ?>">
				<img src="<?php echo $def_img->url; ?>" alt="<?php $def_img->caption; ?>" width="120" height="90" />
			</a>

			<h2 class="title"><a href="<?php echo $story->permalink; ?>" ><?php echo $story->title; ?></a></h2>
			<p class="excerpt"><?php echo $story->excerpt;?></p>
			<div class="meta">
				<?php if( $author ) : ?>
				<span class="author"><?php echo $author;?></span>
				<?php endif; ?>
				<span class="timestamp" title="<?php echo $story->date_gmt;?>"><?php echo $story->date;?></span>
			</div>
		</div>
	<?php endfor; 
		exp_paginate_stories_links( array(
			'prev_text' => '&laquo; Newer',
			'next_text' => 'Older &raquo;',
			'explainer' => false
			) );
	
	?>

</div>
<div class="sidebar span-8 last">
	<?php dynamic_sidebar('Archive Right Sidebar'); ?>
	<div class="widget ad-box-container">
		<div id="ad-box-right-btf" class="ad-box"></div>
	</div>
</div>
<?php
get_footer();