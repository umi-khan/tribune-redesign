<?php

if( false == ( $story instanceof LM_story ) ) return;

$author_id = $author->id;

$html_classes = $story->html_classes;
if( $arguments['is_first'] ) $html_classes .= ' first';
if( $arguments['is_last'] )  $html_classes .= ' last';

$author_attributes    = array( 'width' => 80, 'height' => 80 );
$author_default_image = get_bloginfo( 'template_url' ) . '/img/author-placeholder.gif';
$tooltip_text = (!$arguments['show_excerpt']) ? ' title="'.$story->tooltip.'"' : '';
?>

<div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?> author-story clearfix">
	<a class="image" href="<?php echo $story->permalink; ?>">
		<?php // userphoto_thumbnail( $author_id, '', '', $author_attributes, $author_default_image ); ?>
	</a>

	<h2 class="title" >
		<a href="<?php echo $story->permalink; ?>"<?php echo $tooltip_text; ?>><?php echo $story->title; ?></a>
	</h2>

	<div class="meta">
		<span class="author"><?php echo $author_link;?></span>
		<?php if( $arguments['show_comments'] ) exp_comments_link( $story->id ); ?>
	</div>

	<?php if( $arguments['show_excerpt'] ) : ?><p class="excerpt"><?php echo $story->excerpt; ?></p><?php endif; ?>
</div>
