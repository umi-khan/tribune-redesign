<?php
if( false == ( $story instanceof LM_story ) ) return;

if ( !is_null($arguments['is_mid']) && ($arguments['is_mid']) ){
	$img_dim = array(
		'width'  => 640,
		'height' => 480
	);

}else{
	$img_dim = array(
		'width'  => 144,
		'height' => 108
	);
}
$image_caption = '';
if( isset( $video ) && !is_null($video) && isset($video->thumbnail->url) ) $image_url = $video->thumbnail->url;
else
{
	$img_dim = $image->smart_dimensions( $img_dim['width'], $img_dim['height'] );
	
	if ( (!is_null($arguments['is_mid']) && ($arguments['is_mid'])) || ($arguments['is_first'])  ){
		$image_url      = $image->url;
	}else{
		$image_url      = $image_thumb->url;
	}
	$image_caption  = $image->caption;
}
$img_w = $img_dim['width'];
$img_h = $img_dim['height'];

$html_classes = $story->html_classes;
if( $arguments['is_first'] ) $html_classes .= ' first';
//if( $arguments['is_last'] )  $html_classes .= ' last';
$tooltip_text = (!$arguments['show_excerpt']) ? ' title="'.$story->tooltip.'"' : '';
?>


<div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes ?> long-story">
	<a class="image col-xs-12 col-lg-4 first" href="<?php echo $story->permalink; ?>">
	
		<?php
		$args = array(
			'post_type' => 'attachment',
			'numberposts' => 1,
			'post_parent' => intval($story->id),
			'orderby'          => 'post_date',
			'post_mime_type' => 'image',
			'order'            => 'DESC'
		);
		 $cover_image = wp_get_attachment_image_src($images[0]->ID, 'cover-photo-thumb');
		if( $cover_image[3] ){?> 
			<img src="<?php echo $cover_image[0]; ?>" width="<?php echo $cover_image[1]; ?>" height="<?php echo $cover_image[2]; ?>"></a>
		<?php }else{?>
			<img src="<?php echo $image_url; ?>" alt="<?php $image_caption; ?>">
		</a>

		<?php }?>
 	<div class="content col-xs-12 col-sm-12 col-lg-8">	
 		<h2 class="title"><a class="title" href="<?php echo $story->permalink; ?>"<?php echo $tooltip_text; ?>><?php echo $story->title; ?></a></h2>

	</div>
	</div>

