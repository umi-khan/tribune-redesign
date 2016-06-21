<?php

if( is_array($lm_stories) && !empty($lm_stories) )	:
	$story	      = array_shift($lm_stories);
	$image_manager = new IM_Manager( $story->id, false );
	$image         = $image_manager->default_image;
	$author        = SM_author_manager::get_author_posts_link( $story->id );
?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?> sub-story">
	<h2 class="title"><a href="<?php echo $story->permalink; ?>" ><?php echo $story->title; ?></a></h2>
	<a class="story-image" href="<?php echo $story->permalink; ?>">
		<img src="<?php echo $image->thumbnail->url; ?>" alt="<?php $image->caption; ?>" width="100" height="75" />
	</a>

	<div class="meta">
		<span class="author"><?php echo $author;?></span>
		<span class="timestamp" title="<?php echo $story->date_gmt;?>"></span>
	</div>

	<p class="excerpt"><?php echo $story->excerpt;?></p>
</div>


<?php if( is_array($lm_stories) && !empty($lm_stories) )	: ?>
<div class="more-story">
	<ul class="links">
	<?php
		foreach( $lm_stories as $key => $story) :
			$html_classes	= $story->html_classes;
			if($key == (count($lm_stories)-1)) $html_classes .= ' last';
	?>

		<li id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?>" >
			<a href="<?php echo $story->permalink;?>"><?php echo $story->title; ?></a>
		</li>

	<?php endforeach; ?>
	</ul>
</div>
<?php
	endif;
endif;
?>