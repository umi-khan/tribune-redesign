<?php

if( false == ( $story instanceof LM_story ) ) return;

$html_classes = $story->html_classes;
if( $arguments['is_first'] ) $html_classes .= ' first';
if( $arguments['is_last'] )  $html_classes .= ' last';
$tooltip_text = (!$arguments['show_excerpt']) ? ' title="'.$story->tooltip.'"' : '';
$author = SM_author_manager::get_author_posts_link( $story->id );

?>

<div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?> text-story">
   <a class="title" href="<?php echo $story->permalink; ?>" <?php echo $tooltip_text; ?> >
		<?php echo $story->title; ?>
	</a>
    <div class="meta">
       <span class="author"><?php echo $author; ?></span>
    </div>

	<?php if( $arguments['show_excerpt'] ): ?>
	<p class="excerpt"><?php echo $story->excerpt;?></p>
	<?php endif; ?>
</div>