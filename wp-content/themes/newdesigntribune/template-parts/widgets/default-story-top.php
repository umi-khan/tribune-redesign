<?php
	$story              = $data['post'];
	$template_id        = $data['templateid'];
	$story_id			= (empty($story->ID) || $story->ID == null) ? $data['post_id'] : $story->ID ;
	$links              = $data['links'];

	$widget_id = 'id-'.$story_id;

	$widget_classes = 'top story top-story';
	if(isset($template_id) && ctype_digit($template_id))
	{
		$widget_classes .= ' template-'.$template_id;
	}
	$widget_heading = 'h1';

	$related_links_class= "span-10";
	$related_links_count = HEADLINE1_RELATED_STORIES_COUNT;

	$story_excerpt_chars = 300;
	$story_title_chars = 50;

	if(isset($story->post_excerpt) && $story->post_excerpt != null && !empty($story->post_excerpt))
	{
		$story_excerpt      = $story->post_excerpt;
	}
	else
	{
		$story_excerpt      = exp_get_content_without_images($story->post_content);
	}

    $story_excerpt      = exp_wordwrap_post($story_excerpt, $story_excerpt_chars);
	$story_title        = htmlspecialchars($story->post_title);
	$story_author       = exp_get_author($story_id, $story->post_author);

  	$story_date         = exp_get_postduration($story->post_date, false);
	$story_location     = exp_get_post_location($story_id);
	$permalink          = get_permalink($story_id);
	$story_image        = exp_get_story_image($story_id,"full","left top-story-image");

	$story_comments = "Comments";
	$story_comments .= ($story->comment_count > 1) ? " ($story->comment_count)" : "";

	$email_link       = exp_get_email_link( $story );
?>
<div id="<?php echo $widget_id;?>" class="<?php echo $widget_classes; ?> clearfix">

	<div class="title">
		<?php echo "<$widget_heading".'>' ?>
			<a href="<?php echo $permalink;?>"><?php echo $story_title ;?></a>
		<?php echo "</$widget_heading>" ?>
	</div>

	<div class="span-11">
		<?php echo $story_image; ?>
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

	<div class="last">
		<span class="by-line">
			<?php if($story_author != "") :?>
			<?php echo $story_author;?><br />
			<?php endif;?>
			<span class="timestamp"><?php echo $story_date;?></span>
		</span>

		<p class="body"><?php echo $story_excerpt;?></p>

		<div class="more">
			<?php exp_comments_link($story_id); ?>
			<?php exp_addthis_button($story_id,$permalink,$story_title);?>
			if( !is_null( $email_link )) : ?>
				<a class="email-link" rel="nofollow" href="<?php echo $email_link;?>" title="Email story to friend" target="_blank" >Email</a>
			<?php	endif; ?>
			<!-- @@Facebook banned
			 <div id="fb-root"></div>
			 <?php #exp_insert_script('fb-sdk');?>
			 <div class="fb-like">
				<fb:like colorscheme='evil' href='<?php #echo $permalink; ?>' layout='button_count' show_faces='false' width='100px'/>
			</div>
			-->
			</div>
			
	</div>
</div>