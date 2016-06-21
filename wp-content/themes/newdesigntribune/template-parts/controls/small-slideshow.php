<?php
	if( false == ( $slideshow instanceof SS_slideshow ) ) return;

	$img_dimension = array(
		'width'  => 143,
		'height' => 107
	);
	$image_caption = '';
	if( isset( $video ) ) $image_url = $video->thumbnail->url;
	else
	{
		$img_dimension  = $image->smart_dimensions( $img_dimension['width'], $img_dimension['height'] );
		$image_url      = $image->url;
		$image_caption  = $image->caption;
	}

	$img_w = $img_dimension['width'];
	$img_h = $img_dimension['height'];
?>

<div class="small-slideshow ">

	<div class="content">
		<a class="title" href="<?php echo $slideshow->permalink; ?>"><?php echo $slideshow->title; ?></a>
		<a class="image" href="<?php echo $slideshow->permalink; ?>">
			<img src="<?php echo $image_url; ?>" alt="<?php $image_caption; ?>"
			  width="<?php echo $img_w; ?>" height="<?php echo $img_h; ?>" />
		</a>
	</div>
	
</div>