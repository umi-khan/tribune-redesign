<h5>
	Search Results for <em><?php echo $q; ?></em> -
	<small>(Showing <?php echo $record_num_start; ?> to <?php echo $record_num_end ?> results)</small>:
</h5>

<?php if( false == is_array( $results ) || count( $results ) < 1 ) : ?>

<p>Your query didn't match any post.</p>

<?php else : ?>

<ul>

<?php foreach( $results as $post ) : ?>
	<li id="erppostid-<?php echo $post->ID; ?>">
		<span class="erp_post_title"><?php echo $post->post_title; ?></span> <br />
		<span class="erp_date">
			(Date: <?php echo date( 'F j, Y', strtotime( $post->post_date ) );?>)
		</span> | 
		<a class="erp_post_link" href="<?php echo get_permalink( $post->ID ); ?>" target="_blank">Link</a>
	</li>

<?php endforeach; ?>

</ul>

<p class="erp_pagination">
	<?php if( $show_previous_link ) : ?>
	<a id="erp_pagenum-<?php echo $page_num - 1; ?>" href="#">Previous</a>
	<?php endif; ?>

	<?php if( $show_next_link ) : ?>
	<a id="erp_pagenum-<?php echo $page_num + 1; ?>" href="#">Next</a>
	<?php endif; ?>
</p>

<?php endif; ?>