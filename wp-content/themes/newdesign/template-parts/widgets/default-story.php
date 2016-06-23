<?php 	
	$story              = $data['post'];
	$template_id        = $data['templateid'];
	$story_id           = (empty($story->ID) || $story->ID == null) ? $data['post_id'] : $story->ID ;
	$links              = $data['links'];
	
	$widget_id = 'id-'.$story_id;
	
	$widget_classes = 'story';
	if(isset($template_id) && ctype_digit($template_id))
	{
		$widget_classes .= ' template-'.$template_id;
	}
	
	$widget_heading = 'h2';
	$story_excerpt_chars = 125;
	
	$related_links_class= "";
	$related_links_count = HEADLINE2_RELATED_STORIES_COUNT;
	
	if(isset($story->post_excerpt) && $story->post_excerpt != null && !empty($story->post_excerpt))
	{
		$story_excerpt      = $story->post_excerpt;
	}
	else
	{
		$story_excerpt      = exp_get_content_without_images($story->post_content);
	}
	
    $story_excerpt      = exp_wordwrap_post($story_excerpt, $story_excerpt_chars);
	
    $story_excerpt		= exp_word_break($story_excerpt);
    
	$story_title        = htmlspecialchars($story->post_title);
	$story_author       = exp_get_author($story_id, $story->post_author);
	
    $story_date         = exp_get_postduration($story->post_date, false);
	$story_location     = exp_get_post_location($story_id);
	$permalink          = get_permalink($story_id);
	$story_image        = exp_get_story_image($story_id,"medium","right sub-story-image");
	
	$story_comments = "Comments";
    $story_comments .= ($story->comment_count > 1) ? " ($story->comment_count)" : "";

?>

<div id="<?php echo $widget_id;?>" class="<?php echo $widget_classes; ?> clearfix">

	<h1 class="headline">
		<?php echo "<$widget_heading".'>' ?>
			<a class="title" href="<?php echo $permalink;?>"><?php echo $story_title ;?></a>
		<?php echo "</$widget_heading>" ?>
	</h1>
	
	<?php echo $story_image; ?>
	
	<span class="by-line">
		<?php if($story_author != "") : ?> <?php echo $story_author.'<br/>'; endif; ?>
		
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
