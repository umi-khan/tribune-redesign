<?php

global $wp_query;

if( is_home() || is_tag() ):
	$current_trends = ET_trends::latest_trends( 7 );

	if( is_array( $current_trends ) && count( $current_trends ) > 0 ):
?>
	<div id="trendingbox" class="span-24">
		<strong>Trending:</strong>

		<?php
			foreach( $current_trends as $trend ):
				$class = ( $wp_query->get_queried_object()->term_id == $trend->id ) ? 'selected' : '';
		?>
		<a class="<?php echo $class; ?>" href="<?php echo $trend->url; ?>"><?php echo $trend->name; ?></a>
		<?php endforeach;	?>
	</div>
<?php
	endif;
endif;
?>