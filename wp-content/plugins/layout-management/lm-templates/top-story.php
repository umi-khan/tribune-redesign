<?php

if( is_array($lm_stories) && !empty($lm_stories) )	:

	foreach($lm_stories as $story) :
		$image = $story->image_full;
?>
		<div id="<?php echo $story->html_id;?>" class="<?php echo $story->html_classes; ?> top top-story">
			<div class="headline left">
				<h1 class="title">
					<a href="<?php echo $story->permalink;?>"><?php echo $story->title; ?></a>
				</h1>
			</div>
			<br/>
			<div class="span-11">
				<a href="<?php echo $story->permalink; ?>">
					<img src="<?php echo $image->src; ?>" class="left top-story-image" alt="<?php $image->alt; ?>"
						  width="<?php echo $image->width; ?>" height="<?php echo $image->height; ?>" />
				</a>

				<div class="span-10">
					<?php
						if(function_exists('the_erp_related_posts'))
						{
							the_erp_related_posts($story->id, HEADLINE1_RELATED_STORIES_COUNT);
						}
					?>
				</div>
			</div>
			<div class="last">
				<span class="by-line">
					<?php echo $story->author;?><br />
					<span class="timestamp" title="<?php echo $story->date_gmt;?>"></span>
				</span>
				<p class="body"><?php echo $story->excerpt;?></p>
				<div class="more">
					<?php
						exp_comments_link($story->id);
						exp_addthis_button($story->id, $story->permalink, $story->title);

						if(function_exists('wp_email'))	email_link("", "", true, $story->id);
					?>
					 <div id="fb-root"></div>
					 <?php exp_insert_script('fb-sdk');?>
					 <div class="fb-like">
						<fb:like colorscheme='evil' href='<?php echo  $story->permalink; ?>' layout='button_count' show_faces='false' width='100px'/>
						</div>
					</div>
			</div>
				<div class="clear"></div>
		</div>

<?php
	endforeach;
endif;
?>