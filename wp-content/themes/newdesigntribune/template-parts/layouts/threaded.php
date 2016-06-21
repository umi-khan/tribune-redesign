<?php if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); } ?>

<li class="li-comment" id="li-comment-<?php comment_ID() ?>">
	<div class="div-comment" id="div-comment-<?php comment_ID(); ?>">
<?php
exp_comment($data);
?>
	</div>
<?php
// Dropped </li> is intentional: WordPress figures out where to place the </li> so it can nest comment lists.