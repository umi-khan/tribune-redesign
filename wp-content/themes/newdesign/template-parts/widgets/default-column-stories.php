<?php

	global $LAYOUT_MANAGEMENT;

	if(class_exists('LM_config'))
	{
		$lm_featured_stories_group = $LAYOUT_MANAGEMENT->layout_groups[LM_config::GROUP_FEATURED_STORIES];
		if($lm_featured_stories_group instanceOf LM_layout)
		{
			$lm_featured_stories_group->render_lm_template(LM_config::TEMPLATE_FEATURED_STORIES);
		}
	}
	
	