<?php

if( false == ( $story instanceof LM_story ) ) return;

$img_dim = array(
	'width'  => 144,
	'height' => 108
);
$image_caption = '';
if( isset( $video ) && !is_null($video) && isset($video->thumbnail->url) ) $image_url = $video->thumbnail->url;
else
{
//	$img_dim        = $image->smart_dimensions( $img_dim['width'], $img_dim['height'] );
	$image_url      = $image->url;
	$image_caption  = $image->caption;
}

$img_w = $img_dim['width'];
$img_h = $img_dim['height'];

$html_classes = $story->html_classes;
if( $arguments['is_first'] ) $html_classes .= ' first';
if( $arguments['is_last'] )  $html_classes .= ' last';
$tooltip_text = (!$arguments['show_excerpt']) ? ' title="'.$story->tooltip.'"' : '';
?>

<div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes ?> long-story">
	<a class="image" href="<?php echo $story->permalink; ?>">
		<img src="<?php echo $image_url; ?>" alt="<?php $image_caption; ?>"
		 />
	</a>
	<a class="title" href="<?php echo $story->permalink; ?>"<?php echo $tooltip_text; ?>><?php echo $story->title; ?></a>

	<?php if( $arguments['show_meta'] ) : ?>
	<div class="meta">     
      <?php if( isset($arguments['show_time']) && false !== $arguments['show_time'] ) : ?>
          <?php if(!is_home()) : ?><span class="timestamp" title="<?php echo $story->date_gmt;?>"><?php echo exp_get_pst_date( $story->post_details->post_date, "h:i:A" );?></span><?php endif; ?>
      <?php endif; ?>      
		<?php if( $author ) : ?>
		<span class="author"><?php echo $author;?></span>
		<?php endif;?>
	</div>
	<?php endif; ?>

	<?php if( $arguments['show_excerpt'] ) : ?>
	<p class="excerpt"><?php echo $story->excerpt;?></p>
	<?php endif; ?>	
</div>
