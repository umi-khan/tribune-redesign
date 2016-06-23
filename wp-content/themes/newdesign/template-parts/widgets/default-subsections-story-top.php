<?php 	

	$story              = $data['post'];
	$template_id        = $data['templateid'];
	$story_id			= (empty($story->ID) || $story->ID == null) ? $data['post_id'] : $story->ID ;
	$links              = $data['links'];

	$widget_classes = 'top story top-story left';
	$widget_heading = 'h1';
	
	$related_links_class= "";
	$related_links_count = HEADLINE1_RELATED_STORIES_COUNT;

   $story_excerpt      = exp_wordwrap_post(exp_get_content_without_images($story->post_content), 125);
	$story_title        = htmlspecialchars($story->post_title);
	$story_author       = exp_get_author($story_id, $story->post_author);

  	$story_date         = exp_get_postduration($story->post_date, false);
	$story_location     = exp_get_post_location($story_id);
	$permalink          = get_permalink($story_id);
	$story_image        = exp_get_story_image($story_id,"full","right sub-story-image");
	
	$story_comments = "Comments";
   $story_comments .= ($story->comment_count > 1) ? " ($story->comment_count)" : "";

?>

<div class="<?php echo $widget_classes; ?> clearfix">

	<div class="headline left">
		<?php echo "<$widget_heading".'>' ?>
			<a href="<?php echo $permalink;?>"><?php echo $story_title ;?></a>
		<?php echo "</$widget_heading>" ?>
	</div><br />

	<?php echo $story_image; ?>

	<span class="by-line">
		<?php echo $story_author; ?><br />
		<span class="timestamp"><?php echo $story_date;?></span>
	</span>

	<p class="body"><?php echo $story_excerpt;?></p>


	<div class="<?php echo $related_links_class;?>">
		<?php
			if($links == "related")
			{
				echo get_custom_related_posts_html($story_id,$related_links_count);
			}
			else
			{
				exp_display_story_links_widget($links);
			}
		?>
	</div>
</div>
