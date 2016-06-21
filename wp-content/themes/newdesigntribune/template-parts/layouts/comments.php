<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

global $post, $wp_query;

if( have_comments() || 'open' == $post->comment_status ) :

	$comment_count = 0;

	if( empty( $post->post_password ) || $_COOKIE['wp-postpass_' . COOKIEHASH] == $post->post_password )			
		$comment_count  = $post->comment_count;	
?>

		
		<?php
	if ( count( $wp_query->comments ) > 0) : ?>
	<div class="last">
		<h4 class="comment-heading">COMMENTS <?php if ($comment_count > 0) echo "($comment_count)"; ?></h4>
	</div>
	
			<div class="comments-tabs tabs-container">
			<ul class="tabs ul-tabs clearfix">
				<li>
					<a class="current all-comments" href="#comments">All Comments</a>
				</li>
				<li>
					<a class="recommended-comments" href="#comments">Reader's Recommendations</a>
				</li>
			</ul>

			<div class="tabs-content-group">

		
				<ul class=" toggleContent commentlist hfeed">
					<?php wp_list_comments('type=comment&callback=exp_threaded_comment'); ?>
				</ul>
			<button href="#" class="toggle" style="cursor: pointer;border: 2px solid #3C3B3B;padding: 10px 0px;background: #3C3B3B;width: 210px;border-radius: 25px;color: #fff;font-size: 12px;
	">+ VIEW MORE COMMENT</button>
				
				<?php if( $comment_count > 150 ) : ?>
				<div class="comment-pagination hide-pagination">
					<?php paginate_comments_links(array('prev_text'=>'&laquo; Older Comments', 'next_text'=>'Newer Comments &raquo;'));?>
				</div>
				<?php endif; ?>
			</div>

		</div>
	<br>

	
<?php
	endif;?>
	



<?php
	exp_template_file('comment-form');
endif;
?>
