<?php if( false == isset( $sub_stories_heading ) ) $sub_stories_heading = 'Top News'; ?>

<div class="main group clearfix">
	<div class="col-lg-6 first top-news">
		<h4><?php echo $sub_stories_heading; ?></h4>
		<?php
			$args = array();
			$args['show_excerpt'] = false;

			for ( $i = 0, $j = count( $sub_stories ); $i < $j; $i++ )
			{
				$args['is_first'] = ( $i == 0 ) ? true : false;
				$args['is_last']  = ( $i == $j - 1 ) ? true : false;

				if( $sub_stories[$i] instanceof IDisplayable_control ) $sub_stories[$i]->display( $args );
			}
		?>		
	</div>

	<div class="col-lg-6 last">
		<?php if( $top_story instanceof IDisplayable_control ) $top_story->display(); ?>

		<?php if( is_array( $more_stories ) && count( $more_stories ) > 0 ) : ?>
		<div class="more-story">
			<h4>More News</h4>
			<?php
				$args = array( 'is_first' => false, 'is_last' => false );
				for( $i = 0, $j = count( $more_stories ); $i < $j; $i++ )
				{
					$args['is_first'] = ( $i == 0 ) ? true : false;
					$args['is_last']  = ( $i == $j - 1 ) ? true : false;

					if( $more_stories[$i] instanceof IDisplayable_control ) $more_stories[$i]->display( $args );
				}
			?>
		</div>
		<?php endif; ?>
	</div>
</div>