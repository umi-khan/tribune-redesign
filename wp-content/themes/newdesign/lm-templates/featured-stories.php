<?php

	/*
	 * $lm_stories is a normal array of stories passed to this template file, this array containes objects of layoutmanagement.story class.
	 * All the stories in array will be shown here through a loop
	 */

if( is_array($lm_stories) && !empty($lm_stories) )	:
?>

<div id="featured" class="widget">
	<h1 class="title">Featured</h1>
	<div class="content">
		<ul class="links">
		<?php
		foreach($lm_stories as $key => $story) :
			$author = SM_author_manager::get_author_posts_link( $story->id );
		?>
			<li id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?>">
				<a class="title" href="<?php echo $story->permalink; ?>" ><?php echo $story->title; ?></a>
				<div class="meta">
					<span class="author"><?php echo $author;?></span>
				</div>
			</li>
		<?php
		endforeach;
		?>
		</ul>
	</div>
</div>

<?php
endif;
?>