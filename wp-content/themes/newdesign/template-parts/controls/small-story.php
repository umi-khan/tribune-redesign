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
<?php if(is_home()){ ?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?> small-story">
	<a class="image" href="<?php echo $story->permalink; ?>">
			<img src="<?php echo $image_url; ?>" title="<?php echo $image_caption; ?>" alt=""
				  width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" />
		</a>
	<div class="content clearfix">
		<h2 class="title">
			<a href="<?php echo $story->permalink; ?>" title="<?php echo $story->tooltip; ?>"><?php echo $story->title; ?></a>
		</h2>

	</div>
</div>
<?php } else{ ?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?> small-story">
	<div class="content clearfix">
		<a class="image" href="<?php echo $story->permalink; ?>">
			<img src="<?php echo $image_url; ?>" title="<?php echo $image_caption; ?>" alt=""
				  width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" />
		</a>

		<h2 class="title" <?php echo ( (!is_null($arguments['is_big'])) && ($arguments['is_big']) ) ? "style='font-size:15px;'":"";?>>
			<a href="<?php echo $story->permalink; ?>" title="<?php echo $story->tooltip; ?>"><?php echo $story->title; ?></a>
		</h2>
		<?php if ( (is_null($arguments['off_meta'])) && (!$arguments['off_meta']) ){ ?>
		<div class="excerpt display-none">
			<?php echo $story->excerpt; ?>
		</div>

		<div class="meta">
			<span class="author"><?php echo $author;?></span>
			<?php if(!is_home()) : ?><span class="timestamp" title="<?php echo $story->date_gmt;?>"></span><?php endif; ?>
		</div>
		<?php } ?>
	</div>
</div>
<?php } ?>
