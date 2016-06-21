<?php

if( false == is_array( $stories ) || count( $stories ) < 1 ) return;

$html_classes = '';
if( $arguments['is_first'] ) $html_classes .= ' first';
if( $arguments['is_last'] )  $html_classes .= ' last';

?>

<div class="most-popular <?php echo $html_classes; ?>">
	<h4>Most Popular</h4>
	<?php
	$popular_story_args = array();
	for( $i = 0, $count = count( $stories ); $i < $count; $i++ )
	{
		$popular_story_args['is_last'] = ( $i == $count - 1  ? true : false );

		if( $stories[$i] instanceof IDisplayable_control ) $stories[$i]->display( $popular_story_args );
	}
	?>
</div>