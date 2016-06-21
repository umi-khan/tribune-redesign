<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

global $post, $comment;

extract($data); // for comment reply link
$is_post_author = ( current_user_can( 'edit_page' ) );
$url    = get_comment_author_url();
$author = get_comment_author();

$author_link = ( empty( $url ) || 'http://' == $url ) ? $author : "<a href='$url' rel='external nofollow' target='_blank'>$author</a>";

?>

<div id="comment-<?php comment_ID(); ?>" <?php comment_class('clearfix'); ?>>
	<div class="col-lg-2 meta last">
		<div class="author" title="<?php echo $author_link; ?>" ><?php echo $author_link; ?></div>
		<?php if ($is_post_author) { echo '<div class="ip">'.comment_author_IP().'</div>'; } ?>
		<div class="timestamp" title="<?php echo date('Y-m-d\TH:i:s \G\M\T', strtotime($comment->comment_date_gmt));?>">
			<?php echo date('M j, Y - g:iA', strtotime($comment->comment_date));?>
		</div>

		<?php
		if ( function_exists( 'comment_reply_link' ) ) :
			comment_reply_link(array_merge( $args, array('respond_id' => 'respond-p' . $post->ID, 'depth' => $depth, 'max_depth' => $args['max_depth'])), $comment, $post);
		endif;
		if ( 0 == $depth || $args['max_depth'] <= $depth ) :
		?>
		<div class="reply">
			<a href="<?php the_permalink();?>#respond-p<?php echo $post->ID; ?>" class="comment-reply-link" rel="nofollow">Reply</a>
		</div>
		<?php endif;
		edit_comment_link(__('Edit', 'carrington-blog'), '<div class="edit-comment edit">', '</div>');
		?>
	</div>

	<div class="col-lg-10 last">
		<div class="content">
		<?php comment_text(); ?>				
		</div>
	</div>
</div>