<?php

if( false == ( $story instanceof LM_story ) ) return;



	$img_dimension = array(
		'width'  => 270,
		'height' => 200
	);


$image_caption = '';
if( isset( $video ) ) $image_url = $video->thumbnail->url;
else
{
	//$img_dimension  = $image->smart_dimensions( $img_dimension['width'], $img_dimension['height'] );
	$image_url      = $image->url;
	$image_caption  = $image->caption;
}

$image_width    = $img_dimension['width'];
$image_height   = $img_dimension['height'];

$html_classes = $story->html_classes;
if( $arguments['is_first'] ) $html_classes .= ' first';
if( $arguments['is_last'] )  $html_classes .= ' last';
?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?> small-story">
	<a class="image col-xs-6 col-sm-6 col-lg-4 first" href="<?php echo $story->permalink; ?>">
			<img src="<?php echo $image_url; ?>" title="<?php echo $image_caption; ?>" alt=""
				  width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" />
		</a>
	<div class="content col-xs-6 col-sm-6 col-lg-8 clearfix">
		<h2 class="title">
			<a href="<?php echo $story->permalink; ?>" title="<?php echo $story->tooltip; ?>"><?php echo $story->title; ?></a>
		</h2>

	</div>
</div>

