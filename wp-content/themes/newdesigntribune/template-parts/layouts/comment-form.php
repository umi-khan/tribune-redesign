<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

global $post, $user_ID, $user_identity, $id;

$req = get_option('require_name_email');

// if post is open to new comments
if ('open' == $post->comment_status) {
	// if you need to be regestered to post comments..
	if ( get_option('comment_registration') && !$user_ID ) { ?>

<p id="you-must-be-logged-in-to-comment"><?php printf(__('You must be <a href="%s">logged in</a> to post a comment.', 'carrington-blog'), site_url().'/wp-login.php?redirect_to='.urlencode(get_permalink())); ?></p>
<?php
	}
	else {

?>
<div id="respond-p<?php echo $post->ID; ?>">
	<form action="<?php echo trailingslashit(site_url()); ?>wp-comments-post.php" method="post" class="comment-form" id="commentform" name="commentform">
    <p class="comment-form-user-info tight"></p>

    <div class="span-15 clearfix">
      <?php if ( $user_ID ) : ?>
		 <p class="logged-in tight"><?php printf(__('You are logged in as <a href="%s">%s</a>. ', 'carrington-blog'), site_url().'/wp-admin/profile.php', $user_identity); wp_loginout(); ?>.</p>
      <?php else : ?>
      <p>
      <input type="text" placeholder="Name (required)" id="author-p<?php echo $post->ID; ?>" name="author" class="required" value="" size="22" />

        <input type="text"  placeholder="Email" id="email-p<?php echo $post->ID; ?>" name="email" value="" size="22" />
      </p>
      <!--/email-->
      <?php endif; ?>
    </div>

    <div class="comment-form-comment tight clear">
      <div id="wmd-editor" class="wmd-panel">
        <div id="wmd-button-bar"></div>
        <textarea placeholder="Write your comment here" name="comment" id="wmd-input" class="required" cols="100%" rows="7"></textarea>
      </div>
      <!--<textarea id="comment-p<?php echo $post->ID; ?>" name="comment" rows="8" cols="40"></textarea><br />-->
    </div>
    
    <div class="comment-subscribe">
    	<?php
    		if(function_exists('show_subscription_checkbox'))
	    	{
	    		show_subscription_checkbox(); 
	    	}
    	?>
    </div>
       
    <p class="tight">        
		<input name="submit" class="form-submit" id="comment-submit" type="submit" value="<?php _e('Post comment', 'carrington-blog'); ?>" />
    </p>
    <div>
      <div id="wmd-preview" class="wmd-panel"></div>
    </div>
	 <p class="comment-policy-text">
		 Comments are moderated and generally will be posted if they are on-topic and not abusive. For more information, please see our <a href="<?php echo home_url('comments-policy');?>">Comments FAQ</a>.
	 </p>
    <?php
    	// Hidden fields for comment form with unique IDs, based on post ID, making it safe for AJAX pull.
		$replytoid = isset($_GET['replytocom']) ? (int) $_GET['replytocom'] : 0;
		echo "<input type='hidden' name='comment_post_ID' value='$id' id='comment_post_ID_p$id' />\n";
		echo "<input type='hidden' name='comment_parent' id='comment_parent_p$id' value='$replytoid' />\n";
		
		do_action('comment_form', $post->ID);
?>
  </form>
</div>
<?php 
	} // If registration required and not logged in 
} // If you delete this the sky will fall on your head
?>

<script language="javascript" type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function() {
		var author = _get_author_cookie_info();
		jQuery('#commentform input[name=author]').val(author.name);
		jQuery('#commentform input[name=email]').val(author.email);
		jQuery('#commentform input[name=location]').val(author.city);
		jQuery('#commentform input[name=url]').val(author.url);
	});

	function _get_author_cookie_info() {
		var author = {};
		
		var name  = document.cookie.match(/comment_author_[^=_]+=([^;]+)/);
		var email = document.cookie.match(/comment_author_email_[^=_]+=([^;]+)/);
		var url   = document.cookie.match(/comment_author_url_[^=_]+=([^;]+)/);
		var city  = document.cookie.match(/exp_city=([^;]+)/);

		author.name  = (name && name[1])   ? mydecodeURIComponent(name[1])  : '';
		author.email = (email && email[1]) ? mydecodeURIComponent(email[1]) : '';
		author.url   = (url && url[1])     ? mydecodeURIComponent(url[1])   : '';
		author.city  = (city && city[1])   ? mydecodeURIComponent(city[1])  : '';

		return author;
	}

	function mydecodeURIComponent(str) {
		str = "" + str;
		while(1) {
			var i = str.indexOf ('+');
			if (i < 0) break;

			str = str.substring(0, i) + '%20' + str.substring(i + 1, str.length);
		}
		
		return decodeURIComponent(str);
	}
//]]>
</script>