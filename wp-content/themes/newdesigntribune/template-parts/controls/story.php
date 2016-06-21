<?php

if( false == ( $story instanceof LM_story ) ) return;

$img_dimension = array(
	'width'  => 125,
	'height' => 94
);
$img_dimension_large = array(
	'width'  => 385,
	'height' => 289
);
$image_caption = '';
if( isset( $video ) ) $image_url = $video->thumbnail->url;
else
{
	//$img_dimension  = $image->smart_dimensions( $img_dimension['width'], $img_dimension['height'] );
	$image_url      = $image->url;
	$image_caption  = $image->caption;

	//$img_dimension_large  = $image_large->smart_dimensions( $img_dimension_large['width'], $img_dimension_large['height'] );
	$image_url_large      = $image_large->url;

}

$image_width    = $img_dimension['width'];
$image_height   = $img_dimension['height'];

$image_width_large    = $img_dimension_large['width'];
$image_height_large   = $img_dimension_large['height'];

$top_story = ( $arguments['main_story'] ) ? true : false;
$hidden_story = ( $arguments['hidden'] ) ? true : false;
$mob_story = ( $arguments['mob_story'] ) ? true : false;
$style_bottom = ( $arguments['style_bottom'] ) ? true : false;

$heading_level = ( $arguments['main_story'] ) ? 'h1' : 'h2';

$html_classes = $story->html_classes;
if( $arguments['is_first'] ) $html_classes .= ' first';
if( $arguments['is_last'] )  $html_classes .= ' last';

?>
<?php if(is_home()){ ?>
<?php if($arguments['main_story'] == true){ ?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?> col-xs-12 col-sm-12" <?php if ($hidden_story):?>style="display:none;"<?php endif; ?>>
	<div class="content col-xs-12 col-lg-6 clearfix">
	<<?php echo $heading_level; ?> class="title">
		<a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a>
	</<?php echo $heading_level; ?>>
		<div class="meta">
			<span class="author"><?php echo $author;?></span>
			<?php if(!is_home()) : ?><span class="timestamp" title="<?php echo $story->date_gmt;?>"></span><?php endif; ?>
		</div>
		<p class="excerpt">
			<?php echo $story->excerpt;?>
		</p>
	</div>
		<a class="image col-xs-12 col-lg-6" href="<?php echo $story->permalink; ?>">
			<?php if ($arguments['mob_story'] == true){?>
			<img src="<?php echo $image_url; ?>" alt="<?php echo $image_caption; ?>"
				  width="<?php echo $image_width; ?>" height="<?php echo $image_width; ?>" class="top-story-m" style="display:none;"/>
			<?php }?>
			<img src="<?php echo $image_url_large; ?>" alt="<?php echo $image_caption; ?>"
				  width="<?php echo $image_width_large; ?>" height="<?php echo $image_height_large; ?>" <?php if ($mob_story){?>class="top-story-d"<?php }?>/>
		</a>
		</div>
<?php } else{ ?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?> col-xs-12 col-lg-4" <?php if ($hidden_story):?>style="display:none;"<?php endif; ?>>
	<div class="content clearfix">
	<a class="image" href="<?php echo $story->permalink; ?>">
			<?php if ($arguments['mob_story'] == true){?>
			<img src="<?php echo $image_url; ?>" alt="<?php echo $image_caption; ?>"
				  width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" class="top-story-m" style="display:none;"/>
			<?php }?>

			<img src="<?php echo $image_url_large; ?>" alt="<?php echo $image_caption; ?>"
				  width="<?php echo $image_width_large; ?>" height="<?php echo $image_height_large; ?>" <?php if ($mob_story){?>class="top-story-d"<?php }?>/>
		</a>
	<<?php echo $heading_level; ?> class="title">
		<a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a>
	</<?php echo $heading_level; ?>>
		<div class="meta">
			<span class="author"><?php echo $author;?></span>
			<?php if(!is_home()) : ?><span class="timestamp" title="<?php echo $story->date_gmt;?>"></span><?php endif; ?>
		</div>
		<p class="excerpt">
			<?php echo $story->excerpt;?>
		</p>
	</div>	
	</div>
<?php } ?>
<?php }else{ ?>
<div id="<?php echo $story->html_id; ?>" class="<?php echo $html_classes; ?>" <?php if ($hidden_story):?>style="display:none;"<?php endif; ?>>
	<<?php echo $heading_level; ?> class="title">
		<a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a>
	</<?php echo $heading_level; ?>>

	<div class="content clearfix">
		<a class="image" href="<?php echo $story->permalink; ?>">
			<?php if ($mob_story){?>
			<img src="<?php echo $image_url_large; ?>" alt="<?php echo $image_caption; ?>"
				  width="<?php echo $image_width_large; ?>" height="<?php echo $image_height_large; ?>" class="top-story-m" style="display:none;"/>
			<?php }?>

			<img src="<?php echo $image_url; ?>" alt="<?php echo $image_caption; ?>"
				  width="<?php echo $image_width; ?>" height="<?php echo $image_height; ?>" <?php if ($mob_story){?>class="top-story-d"<?php }?>/>
		</a>

		<?php if ($mob_story && $style_bottom){?>

		<<?php echo $heading_level; ?> class="title h-style-mob" style="display:none;">
			<a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a>
		</<?php echo $heading_level; ?>>

		<?php }?>

		<div class="meta">
			<span class="author"><?php echo $author;?></span>
			<?php if(!is_home()) : ?><span class="timestamp" title="<?php echo $story->date_gmt;?>"></span><?php endif; ?>
		</div>
		<p class="excerpt">
			<?php echo $story->excerpt;?>
		</p>

		<?php // exp_comments_link( $story->id ); ?>
	</div>

	<?php if( !$arguments['main_story'] && isset($related_stories) && is_array( $related_stories ) ) : ?>
	<ul class="links related-stories">
		<?php foreach( $related_stories as $rel_story ) : ?>
		<li>
			<a href="<?php echo get_permalink( $rel_story->ID ); ?>"><?php echo $rel_story->post_title; ?></a>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>
<?php } ?>