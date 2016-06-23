<?php
	if( false == ( $story instanceof LM_story ) ) return;

	$img_dim = array(
		'width'  => 143,
		'height' => 107
	);
	$image_caption = '';
	if( isset( $video ) && !is_null($video) ) $image_url = $video->thumbnail->url;
	else
	{
		$img_dim        = $image->smart_dimensions( $img_dim['width'], $img_dim['height'] );
		$image_url      = $image->url;
		$image_caption  = $image->caption;
	}

	$img_w = $img_dim['width'];
	$img_h = $img_dim['height'];
	$imagesize = '160x120';
	$imagesizenew = '640x480';
	$image_url = str_replace($imagesize,$imagesizenew,$image_url); 
	if( $arguments['is_first'] ) $html_classes .= ' first';
	if( $arguments['is_last'] )  $html_classes .= ' last';

?>
<div class="<?php echo $story->html_classes; ?>">
<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?> small-picture-story">
	<div class="content">
		<a class="image" href="<?php echo $story->permalink; ?>">
			<img src="<?php echo str_replace( 'http://', 'http://i0.wp.com/', $image_url ); ?>" alt="<?php $image_caption; ?>"
			  width="100%" height="auto" />
		</a>
		<a class="title" href="<?php echo $story->permalink; ?>" title="<?php echo $story->tooltip; ?>"><?php echo $story->title; ?></a>
	</div>
	</div>
</div>
