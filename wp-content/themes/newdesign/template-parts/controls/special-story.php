<?php

if( false == ( $story instanceof LM_story ) ) return;

$img_dimension = array(
	'width'  => 418,
	'height' => 314
);
?>

<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?> special-story">
	<h1 class="title">
		<a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a>
	</h1>

	<div class="breaking-text">Breaking News</div>

	<div class="content clearfix clear">
		<div class="span-11 last">
			<?php
			if( isset( $video ) ) : 
				$video->player( $img_dimension['width'], $img_dimension['height']);
			else :
				$img_dimension = $image->smart_dimensions( $img_dimension['width'], $img_dimension['height'] );
				?>
			<a class="image" href="<?php echo $story->permalink; ?>">
				<img src="<?php echo $image->url; ?>" alt="<?php echo $image->caption; ?>"
					  width="<?php echo $img_dimension['width']; ?>" height="<?php echo $img_dimension['height']; ?>" />
			</a>
			<?php endif; ?>
		</div>

		<div class="span-5 last">
			<p class="excerpt">
				<?php echo $story->excerpt;?>
			</p>
			<div class="meta">
				<?php if(!is_home()) : ?><span class="timestamp" title="<?php echo $story->date_gmt;?>"></span><?php endif; ?>
				<span class="comment"><?php exp_comments_link( $story->id ); ?></span>
			</div>
			
			<?php
			$num_related_posts_to_show = ceil( $img_dimension['height'] / 80 );
			$related_stories = array_slice( $related_stories, 0, $num_related_posts_to_show );

			if( is_array( $related_stories ) ) : ?>
			<ul class="links related-stories">
				<?php foreach( $related_stories as $rel_story ) : ?>
				<li>
					<a href="<?php echo get_permalink( $rel_story->ID ); ?>"><?php echo $rel_story->post_title; ?></a>
				</li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</div>
	</div>
</div>