<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

global $LAYOUT_MANAGEMENT;
$lm_main_stories_group = $LAYOUT_MANAGEMENT->layout_groups[LM_config::GROUP_MAIN_STORIES];

$class = (isset($class)) ? $class : "span-16";

?>

<div class="<?php echo $class?> primary">
	<?php $lm_main_stories_group->render_lm_template(LM_config::TEMPLATE_TOP_STORY); ?>
	  
	<div class="span-8 primary">
		<?php $lm_main_stories_group->render_lm_template(LM_config::TEMPLATE_SUB_STORY); ?>
		<div class="more-story">
			<h4>More News</h4>
			<?php $lm_main_stories_group->render_lm_template(LM_config::TEMPLATE_MORE_STORIES); ?>
		</div>
	</div>
   	
	<div class="span-8 sidebar last"><?php dynamic_sidebar('Section Default Middle Sidebar'); ?></div>
</div>