<?php
$template_url= get_bloginfo('template_url', 'display');
$p_width	 = 520;
$p_height	 = 340;
?>

<script src='<?php echo $template_url; ?>/js/AC_RunActiveContent.js' type='text/javascript'></script>

<?php foreach($videos as $video) :
        $main_post_duration = exp_get_postduration($video->post_date_gmt, TRUE);
    ?>
	<div class="left" >

            
                <h2 class="prepend-top">
                    <?php echo ($video->post_excerpt != "") ? $video->post_excerpt : ucwords($video->post_title);?>
                </h2>
                <?php if($video->post_content != "") :?>
		<div class="span-15 append-bottom">
                    <p class="body"><?php echo $video->post_content;?></p>
		</div>
                 <?php endif; ?>
                <div class="meta">
                    <span class="timestamp"><?php echo $main_post_duration;?></span>
                </div>
		<div class="span-15">
                    <?php switch($video->post_mime_type):
                            case "video/x-flv":
                    ?>
                                <script type='text/javascript' language='javascript'>
                                     AC_FL_RunContent('codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0', 'width', '<?php echo $p_width;?>', 'height', '<?php echo $p_height;?>', 'src', ((!DetectFlashVer(9, 0, 0) && DetectFlashVer(8, 0, 0)) ? 'player8' : '<?php echo $template_url; ?>/img/player'), 'pluginspage', 'http://www.macromedia.com/go/getflashplayer', 'id', 'flvPlayer', 'allowFullScreen', 'true', 'movie', ((!DetectFlashVer(9, 0, 0) && DetectFlashVer(8, 0, 0)) ? 'player8' : '<?php echo $template_url; ?>/img/player'), 'FlashVars', 'movie=<?php echo $video->guid;?>');
                                </script>
                                <noscript>
                                 <object width='<?php echo $p_width;?>' height='<?php echo $p_height;?>' id='flvPlayer'>
                                  <param name='allowFullScreen' value='true' />
                                  <param name='movie' value='<?php echo $template_url; ?>/img/player.swf?movie=<?php echo $video->guid;?>' />
                                 </object>
                                </noscript>
                    <?php   break;
                            case  "video/avi":
                            case  "video/mpeg":
                                ?>
                               <OBJECT ID="MediaPlayer" classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715" width=<?php echo $p_width;?> height=<?php echo $p_height;?> standby="Loading Microsoft Windows Media Player components..." type="application/x-oleobject">
                                    <PARAM NAME="FileName" VALUE="<?php echo $video->guid;?>">
                                    <PARAM NAME="TransparentAtStart" Value="false">
                                    <PARAM NAME="AutoStart" Value="false">
                                    <PARAM NAME="AnimationatStart" Value="true">
                                    <PARAM NAME="ShowControls" Value="true">
                                    <PARAM NAME="ShowPositionControls" Value="false">
                                    <PARAM NAME="ShowStatusBar" Value="true">
                                    <PARAM NAME="autoSize" Value="false">
                                    <PARAM NAME="displaySize" Value="0">
                                    <Embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/isapi/redir.dll?prd=windows&sbp=mediaplayer&ar=Media&sba=Plugin&" src="<?php echo $video->guid;?>" Name=MediaPlayer AutoStart=0 Width=<?php echo $p_width;?> Height=<?php echo $p_height;?> transparentAtStart=0 animationAtStart=1 ShowControls=1 ShowPositionControls=0 ShowStatusBar=1 autoSize=0 displaySize=0></embed>
                                </OBJECT>
                    <?php   break;
                            case  "video/mp4":
                            case  "video/mov":
                                ?>
                                <OBJECT CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" CODEBASE="http://www.apple.com/qtactivex/qtplugin.cab" WIDTH="<?php echo $p_width;?>" HEIGHT="<?php echo $p_height;?>" >
                                <PARAM NAME="src" VALUE="<?php echo $video->guid;?>" >
                                <PARAM NAME="autoplay" VALUE="false" >
                                <PARAM name="CONTROLLER" VALUE="true">
                                <EMBED SRC="<?php echo $video->guid;?>" TYPE="image/x-macpaint" PLUGINSPAGE="http://www.apple.com/quicktime/download" WIDTH="<?php echo $p_width;?>" HEIGHT="<?php echo $p_height;?>" AUTOPLAY="false" CONTROLLER="true"></EMBED>
                                </OBJECT>
                     <?php   break;
                            case  "video/url":
                                ?>
                                    
                                    <object width="<?php echo $p_width;?>" height="<?php echo $p_height;?>">
                                        <param name="movie" value="<?php echo $video->guid;?>&hl=en_US&fs=1&">
                                        <param name="allowFullScreen" value="true">
                                        <param name="allowscriptaccess" value="always">
                                        <embed src="<?php echo $video->guid;?>&hl=en_US&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="<?php echo $p_width;?>" height="<?php echo $p_height;?>">
                                        </embed>
                                    </object>

                    <?php
                            break;
                            default:
                    ?>            
                    <img alt="<?php echo $video->title;?>" src="<?php echo $video->guid;?>" style="width:<?php echo $p_width;?>;height:<?php echo $p_height;?>;">
                    <?php
                          endswitch;?>

		</div>
          
	</div>

<?php endforeach; ?>
