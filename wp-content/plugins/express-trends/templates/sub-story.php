<?php $wp_theme_url = get_bloginfo( 'template_url' ); ?>

<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?> sub-story clearfix">
	<a href="<?php echo $story->permalink; ?>">
		<img class="story-image" src="<?php echo $image->thumbnail->url; ?>" alt="<?php $image->caption; ?>" />
	</a>

	<h1 class="title"><a href="<?php echo $story->permalink; ?>" ><?php echo $story->title; ?></a></h1>

	<div class="meta">
		<span class="author"><?php echo $author;?></span>
		<span class="timestamp"><?php echo $story->date;?></span>
	</div>

	<p class="excerpt"><?php echo $story->excerpt;?></p>

	<div class="story-social-links clearfix">
		<div class="comments-link">
			<a href="<?php echo $story->permalink; ?>#comments" title="Comments">
				<img src="<?php echo $wp_theme_url; ?>/img/comment-18.gif" alt="" />
				<span class="comment-count"><?php echo $story->comment_count; ?></span>
			</a>
		</div>
		<div class="twitter-link">
			<a href="http://twitter.com/share" class="twitter-share-button"
				data-count="horizontal" data-url="<?php echo $story->permalink; ?>">Tweet</a>
		</div>
		<div class="fb-link">
			<fb:like colorscheme="evil" href="<?php echo $story->permalink; ?>" layout="button_count"
						show_faces="false" width="100px"></fb:like>
		</div>
	</div>
</div>