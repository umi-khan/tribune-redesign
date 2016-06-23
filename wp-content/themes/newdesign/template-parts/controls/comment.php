<?php

if( $arguments['is_first'] ) $html_classes .= ' first';
if( $arguments['is_last'] )  $html_classes .= ' last';

?>
<div class="comment <?php echo $html_classes?>">
	<h2 class="title">
		<a href="<?php echo $story->permalink; ?>"><?php echo $story->title; ?></a>
	</h2>
	<span class="author"><?php echo $author; ?></span>
	<p class="excerpt"><?php echo $comment->comment_content; ?></p>
</div>