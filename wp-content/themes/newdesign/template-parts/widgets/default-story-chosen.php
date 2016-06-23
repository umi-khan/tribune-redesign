<?php 	
	$post = $data['post'];
	$template_id = $data['templateid'];
	$post_id =  $data['post_id'];
	
	$content = exp_get_content_without_images($post->post_content);
?>

        <div class="story">
            <?php echo exp_get_story_image($post->ID, 'thumbnail', 'left'); ?>
            <h3>
                <a href="<?php echo get_permalink($post->ID);?>"><?php echo htmlspecialchars($post->post_title);?></a>
            </h3>
            <p class="body"><?php echo exp_wordwrap_post($content, 80);?></p>
	</div>
