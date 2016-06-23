<?php /* Template Name: epaper */ ?>
<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }


exp_insert_css('epaper');

get_header();
$city = $_GET['city'];
?>

<div class="span-24">
    <style>
		.epaperlinks {overflow:hidden; margin-bottom:20px}
.epaperlinks li{float:left; list-style:none; margin-right:10px; font-size:14px}
		</style>
    <div style="display:none;" class="epaperlinks">
      <ul>
        <li><a href="/epaper/?city=Karachi">Karachi</a></li>
        <li><a href="/epaper/?city=Lahore">Lahore</a></li>
        <li><a href="/epaper/?city=Islamabad">Islamabad</a></li>
        <li><a href="/epaper/?city=Peshawar">Peshawar</a></li>
      </ul>
    </div>
  <div class="epaper-container" style="width:auto;height:1017px;">

    <?php if (($city=='Karachi') || ($city=='')) {?>
    <iframe src="http://epaper.tribune.com.pk/" id="trib-epaper" scrolling="no" frameborder="0" width="100%" height="1017"></iframe>
    <?php } else if($city=='Peshawar') { ?>
    <iframe src="http://etribune.express.com.pk" id="trib-epaper" class="peshawar" scrolling="no" frameborder="0"></iframe>
    <?php } elseif ($city=='Lahore'){ ?>
    <iframe src="http://etribune.express.com.pk" id="trib-epape" class="lahore" scrolling="no" frameborder="0"></iframe>
    <?php } elseif ($city=='Islamabad'){?>
    <iframe src="http://etribune.express.com.pk" id="trib-epaper" class="islamabad" scrolling="no" frameborder="0"></iframe>
    <?php }  ?>
  </div>
</div>
<?php get_footer(); ?>
