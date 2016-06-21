<?php

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

// current category
global $wp_query;
$category_id = $wp_query->get_queried_object()->cat_ID;

$layout = new LM_layout( $category_id, LM_config::GROUP_MAIN_STORIES );
?>

<div class="primary">
	<?php $layout->render_lm_template( LM_config::TEMPLATE_TOP_STORY ); ?>
	
	<div class="col-lg-6">
		<?php $layout->render_lm_template( LM_config::TEMPLATE_SUB_STORY ); ?>
		<div class="more-story">
			<h4>More News</h4>
			<?php $layout->render_lm_template( LM_config::TEMPLATE_MORE_STORIES ); ?>
		</div>
	</div>

	<div class="col-lg-6 sidebar last"><?php dynamic_sidebar( 'Subsection Default Middle Sidebar' ); ?></div>
</div>
		</div>
