<?php

$num_long_stories = 4;

$business_cat    = get_category_by_slug( 'pakistan' );
$business_layout = new LM_layout( $business_cat->cat_ID, LM_config::GROUP_MAIN_STORIES, true, 9 );

$business_cat_link = get_category_link( $business_cat->cat_ID );

$img_dimension = array(
	'width'  => 230,
	'height' => 172
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

<h3 class="m-group">
	<a href="<?php echo $business_cat_link; ?>"><?php echo $business_cat->cat_name; ?></a>
</h3>
	<?php
	$small_story_args = array();
	for( $i = 0; $i < 4; $i++ )
	{
		$small_story_args['is_first'] = $i == 0;
		$small_story_args['is_last']  = $i == 4 - 1;

		$story = new Control_small_story( array_shift( $business_layout->posts_stack ) );
		$story->display( $small_story_args );
	}
	?>

