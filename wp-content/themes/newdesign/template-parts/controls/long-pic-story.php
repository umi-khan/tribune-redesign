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
<?php if(is_home()){ ?>
<?php if ( $arguments['is_first'] ){ ?>
<div class="featured-main">
<h4><span>FEATURED STORIES</span></h4>
<?php } ?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo ( $arguments['is_first'] || $arguments['is_mid'] ) ? "col-xs-12" : "col-xs-6 col-lg-6";?> <?php echo $html_classes ?> long-story">
	<a class="image col-xs-12 col-lg-6" href="<?php echo $story->permalink; ?>">
		<?php if ( $arguments['is_first'] || $arguments['is_mid'] ) :?>
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
 	<div class="content col-xs-12 col-lg-6">	
 		<h2 class="title"<?php if ( (!is_null($arguments['is_big'])) && ($arguments['is_big']) ){ ?> style="font-size:20px;"<?php }?>><a class="title" href="<?php echo $story->permalink; ?>"<?php echo $tooltip_text; ?>><?php echo $story->title; ?></a></h2>
	<?php else :?>
		<img src="<?php echo $image_url; ?>" alt="<?php echo $image_caption; ?>" width="<?php echo $img_w; ?>" height="<?php echo $img_h; ?>" /></a>
	<div class="content col-xs-12 col-lg-6">
		<a class="title" href="<?php echo $story->permalink; ?>"<?php echo $tooltip_text; ?>><?php echo $story->title; ?></a>
		<?php endif; ?>  
	<div class="meta">     
     
          <?php if(!is_home()) : ?><span class="timestamp" title="<?php echo $story->date_gmt;?>"><?php echo exp_get_pst_date( $story->post_details->post_date, "h:i:A" );?></span>    
		<?php if( $author ) : ?>
		<span class="author"><?php echo $author;?></span>
		<?php endif;?>
	</div>
	<p class="excerpt"><?php echo $story->excerpt;?></p>
	<?php endif; ?>
	</div>
	</div>
</div>
<?php } else{ ?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo ( $arguments['is_first'] || $arguments['is_mid'] ) ? "" : "col-lg-6";?> <?php echo $html_classes ?> long-story" <?php echo ( $arguments['is_first']) ? "style='margin-top:10px'" : "";?>>
	<a class="image" href="<?php echo $story->permalink; ?>">
		<?php if ( $arguments['is_first'] || $arguments['is_mid'] ) :?>
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
		<div style="width:100%; height:170px; overflow:hidden;">
			<img src="<?php echo $image_url; ?>" alt="<?php $image_caption; ?>">
		</div>
		</a>

		<?php }?>
 		
 		<h2 class="title"<?php if ( (!is_null($arguments['is_big'])) && ($arguments['is_big']) ){ ?> style="font-size:20px;"<?php }?>><a class="title" href="<?php echo $story->permalink; ?>"<?php echo $tooltip_text; ?>><?php echo $story->title; ?></a></h2>

	<?php else :?>
		<img src="<?php echo $image_url; ?>" alt="<?php echo $image_caption; ?>" width="<?php echo $img_w; ?>" height="<?php echo $img_h; ?>" /></a>
		<a class="title" href="<?php echo $story->permalink; ?>"<?php echo $tooltip_text; ?>><?php echo $story->title; ?></a>
		<?php endif; ?>  
	

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
<?php if( ($arguments['is_last']) && !($arguments['is_end']) ){?>
<div class="col-lg-12 last" style="border-bottom:1px dotted #d2d0c2; margin: 10px 0 20px;" ></div>
<?php } } ?>
