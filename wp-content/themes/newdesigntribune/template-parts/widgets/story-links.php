<?php
	$links = $data["links"];
	$show_comment_count = $data["show_comment_count"];
	$links_title_length = isset($data["links_title_length"]) ? $data["links_title_length"] : 45;
	$story_comments = "";
	
	if (!empty($links) && $links != false) :
?>
		<ol class="links">
	
		<?php 
			$link_class = "";
			foreach($links as $key=>$link):
				$__post = (isset($link->post_title)) ? $link : get_post($link->postid);
				if ($__post) :
				
					$link_id = 'id-'.$__post->ID;
					
					$link_class = 'story';
					if(isset($link->templateid) && ctype_digit($link->templateid))
					{
						$link_class .= ' template-'.$link->templateid;
					}
					
					if($key == (count($links)-1))
					{
						$link_class .= ' last';
					} 
					
					
					$link_title = esc_html($__post->post_title);
					if($show_comment_count == true && isset($link->comment_count))
					{
						$story_comments = "($link->comment_count&nbsp;comment";
						if($link->comment_count > 1)
						{
							$story_comments .= 's';
						}
						$story_comments .= ')';
					}
				
		?>
					<li id="<?php echo $link_id; ?>" class="<?php echo $link_class;?>" >
						<a class="title" href="<?php echo get_permalink($__post->ID);?>"><?php echo $link_title; ?></a>
						<?php if($story_comments != null && !empty($story_comments)) : ?>
							<span class="comments"><?php echo $story_comments;?></span>
						<?php endif;?>
					</li>
				<?php endif; ?>
		<?php endforeach; ?>
		</ol>
	<?php endif; ?>