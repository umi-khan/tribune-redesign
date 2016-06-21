
function commentsvote_add(post_id,comment_id,nonce) {
  jQuery.ajax({
        type: 'POST',
        url: votecommentajax.ajaxurl,
        data: {
            action: 'commentsvote_ajaxhandler',
            p_id: post_id,
            commentid: comment_id,
            nonce: nonce
        },
        success: function(data, textStatus, XMLHttpRequest) {
            var linkofcomment = '#commentsvote-' + comment_id;
            jQuery(linkofcomment).html('');
            jQuery(linkofcomment).append(data);
        },
    });
 
}