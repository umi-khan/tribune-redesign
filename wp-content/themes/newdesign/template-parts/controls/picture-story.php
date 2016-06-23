<?php if( false == ( $story instanceof LM_story ) ) return;

	$img_dimension = array(
		'width'  => 370,
		'height' => 195
	);

	if( is_category() )
	{
		$story_category_id = $story->category_id;
		if( $story->category_id )
		{
			$story_category = get_category( $story->category_id );
			if( $story_category && $story_category->parent == 0 )
			{
				$img_dimension['width']  = 385;
				$img_dimension['height'] = 289;
			}
		}
	}
	$category_class = ( $category->parent ) ? $category->parent->slug : $category->slug;
	$tooltip_text = (!$arguments['show_excerpt']) ? ' title="'.$story->tooltip.'"' : '';	

?>
<?php if(is_home()){ ?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?> picture-story clearfix">
		<?php
		if( isset( $video ) ) :
			$video->player( $img_dimension['width'], $img_dimension['height']);
		else :
			$img_dimension = $image->smart_dimensions( $img_dimension['width'], $img_dimension['height'] );
		?>
		<a class="image col-lg-6" href="<?php echo $story->permalink; ?>">
			<img src="<?php echo $image->url; ?>" alt="<?php echo $image->caption; ?>"
				  width="<?php echo $img_dimension['width']; ?>" height="<?php echo $img_dimension['height']; ?>" />
		</a>
		<?php endif; ?>
	<div class="content col-lg-6">
		<h2 class="<?php echo (is_home() ) ? "head-line" : "title".$category_class; ?>"<?php echo $tooltip_text; ?>>
			<a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a>
		</h2>
		<div class="meta">
			<?php if( $author ) : ?>
			<span class="author"><?php echo $author;?></span>
			<?php endif;?>
			<span class="timestamp" title="<?php echo $story->date_gmt;?>"></span>
		</div>
		<p class="excerpt">
			<?php echo $story->excerpt;?>
		</p>	

	</div>
</div>
<?php } else{ ?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?> picture-story clearfix">
	<div class="content">
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
		<h2 class="<?php echo (is_home() ) ? "head-line" : "title".$category_class; ?>"<?php echo $tooltip_text; ?>>
			<a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a>
		</h2>
		<?php if( !is_home() ): ?>
		<?php if( $arguments['show_meta'] ) : ?>
		<div class="meta">
			<?php if( $author ) : ?>
			<span class="author"><?php echo $author;?></span>
			<?php endif;?>
			<span class="timestamp" title="<?php echo $story->date_gmt;?>"></span>

			<?php exp_comments_link( $story->id ); ?>
		</div>
		<?php endif; ?>
	

		<?php if( $arguments['show_excerpt'] ) : ?><p class="excerpt"><?php echo $story->excerpt;?></p><?php endif; ?>	
	<?php endif; ?>
	</div>
</div>
 <?php } ?>