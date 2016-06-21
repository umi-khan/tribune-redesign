<?php $image_dimensions = $image->large->smart_dimensions( 305, 229 ); ?>

<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?> top top-story clearfix">
	<div class="content">
		<div class="span-8">
			  <h1 class="title"><a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a></h1>

			<div class="social">
				<?php
				exp_comments_link($story->id);
				exp_addthis_button($story->id, $story->permalink, $story->title);
				if(function_exists('wp_email'))	exp_email_link( $story );
				?>
			</div>

			<div class="meta">
				<span class="author"><?php echo $author;?></span>
				<span class="timestamp" title="<?php echo $story->date_gmt;?>"></span>
			</div>

			<p class="excerpt">
				<?php echo $story->excerpt;?>
			</p>

			<?php if(function_exists('the_erp_related_posts')) the_erp_related_posts($story->id, HEADLINE1_RELATED_STORIES_COUNT); ?>

		</div>


		<div class="span-8 last">
			<a class="top-story-image" href="<?php echo $story->permalink; ?>">
				<img src="<?php echo $image->large->url; ?>" alt="<?php $image->caption; ?>"
					  width="<?php echo $image_dimensions['width']; ?>" height="<?php echo $image_dimensions['height']; ?>" />
			</a>
		</div>
	</div>
</div>