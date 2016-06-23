<?php
if( is_category('sports') || is_home() || is_tag() ):
	global $wp_query;
	$current_trends = ET_trends::latest_trends( 6 );

	$current_trends_count = is_array( $current_trends ) ? count( $current_trends ) : 0;
?>
<?php
	if (is_tag())
		$current_tag=$wp_query->get_queried_object()->slug;

	$sports_tag = array("cricket", "football", "tennis", "motogp");

 if ( is_category('sports') || ( is_tag() && ( in_array( $current_tag, $sports_tag) ) ) ) : ?>
	<div id="trendingbox" class="span-14">
 	<strong>Topics:</strong>
 	<?php $sports_counter = 0;
				foreach( $sports_tag as $tag ):
 					$class = ( $sports_counter++ == 0 ) ? 'first' : ( $sports_counter == count($sports_tag) ? 'last' :  '' ) ;
					$class .= ( is_tag() && ( $current_tag == $tag ) ) ? ' selected' : '';
			?><a class="<?php echo $class; ?>" href="/<?php echo $tag; ?>"><?php echo $tag; ?></a><?php endforeach;?>
	</div>
	<?php
	else:
	?>
	<?php  if( $current_trends_count > 0 ):?>
		<div id="trendingbox" class="span-14">
			<strong>Topics:</strong>
			<?php $counter = 0;
				foreach( $current_trends as $trend ):
					if ( ( trim(strtolower($trend->name)) == 'anniversary') OR ( in_array(trim(strtolower($trend->name)), $sports_tag ) ) ) continue;
					$class = ( $counter++ == 0 ) ? 'first' : ( $counter == $current_trends_count ? 'last' :  '' ) ;
					$class .= ( is_tag() && $wp_query->get_queried_object()->term_id == $trend->id ) ? ' selected' : '';
			?><a class="<?php echo $class; ?>" href="<?php echo $trend->url; ?>"><?php echo $trend->name; ?></a><?php endforeach;?>
		
		</div>
		<?php
			endif;
		endif;
endif;
?>

