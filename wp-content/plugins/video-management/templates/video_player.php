<?php 
if (strpos($video->url, 'youtube.com') > 0){ //Youtube
   
   $yt_url = $video->url;
   $video_url_array = array();
   parse_str(parse_url($yt_url, PHP_URL_QUERY), $video_url_array);
   $video_id = $video_url_array['v'];

   if(strlen($video_id) == 0 ) return; 
   $embed_url   = "//www.youtube.com/embed/$video_id";
   
}elseif (strpos($video->url, 'jwplatform.com') > 0){ //JWPlatform
   
   $embed_url = $video->url;

}elseif (strpos($video->url, 'vimeo.com') > 0) { //Vimeo
   
   $video_id = (int) substr(parse_url($video->url, PHP_URL_PATH), 1);
   if($video_id == 0 ) return;  
   $embed_url = "//player.vimeo.com/video/{$video_id}";

} elseif (strpos($video->url, 'facebook.com') > 0) { //Facebook

   $video_id = parse_url($video->url);
   parse_str($video_id['query']);
   if (!$v)
   {
      $str = explode("/", $video->url);
      $v = $str[count($str)-2];
   }
   $video_id = $v;
   if(strlen($video_id) == 0 ) return; 
   $embed_url = "//www.facebook.com/video/embed?video_id={$video_id}";

} elseif (strpos($video->url, 'dailymotion.com') > 0) { //Dailymotion

   $video_id = basename($video->url);
   if(strlen($video_id) == 0 ) return;   
   $embed_url ="//www.dailymotion.com/embed/video/{$video_id}";

} else return;
?>

<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style>

<?php if (strpos($video->url, 'facebook.com') > 0) { //Facebook ?>

   <?php if ( ! is_admin() ) {?>
   <!-- Facebook Video with Player Button  for frontend -->
   <div class='embed-container'><div id="fb-root"></div><script>(function(d, s, id) {  var js, fjs = d.getElementsByTagName(s)[0];  if (d.getElementById(id)) return;  js = d.createElement(s); js.id = id;  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";  fjs.parentNode.insertBefore(js, fjs);}(document, 'script', 'facebook-jssdk'));</script><div class="fb-video" data-allowfullscreen="1" data-href="<?php echo $video->url;?>"><div class="fb-xfbml-parse-ignore">
   </div></div></div>

   <?php } else { ?>

   <div class='embed-container'><iframe src='<?php echo $embed_url;?>' frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>
   
   <?php } ?>

<?php }else{?>
   <div class='embed-container'><iframe src='<?php echo $embed_url;?>' frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>
<?php }?>