<?php
if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

get_header();
?>

        <!-- Primary Column -->
	<div class="span-18 primary">
		<object style="width: 700px; height: 538px">
		<param name="movie"
			value="http://static.issuu.com/webembed/viewers/style1/v1/IssuuViewer.swf?mode=embed&amp;showFlipBtn=true&amp;folderId=7d3f4c65-1327-4237-88c5-424a9bf17605&amp;layout=http%3A%2F%2Ftribune.com.pk%2Fwp-content%2Fissuu%2Ftribune%2Flayout.xml" />
		<param name="allowFullScreen" value="true" />
		<param name="menu" value="false" />
		<embed
			src="http://static.issuu.com/webembed/viewers/style1/v1/IssuuViewer.swf"
			type="application/x-shockwave-flash" allowFullScreen="true"
			menu="false" style="width: 700px; height: 538px"
			flashvars="mode=embed&amp;showFlipBtn=true&amp;folderId=7d3f4c65-1327-4237-88c5-424a9bf17605&amp;layout=http%3A%2F%2Ftribune.com.pk%2Fwp-content%2Fissuu%2Ftribune%2Flayout.xml" 
		/>
	</object>
		
	</div>
	
	<div class="span-6 sidebar last"><?php  dynamic_sidebar('Magazine Right Sidebar'); ?></div>         
<?php get_footer(); ?>