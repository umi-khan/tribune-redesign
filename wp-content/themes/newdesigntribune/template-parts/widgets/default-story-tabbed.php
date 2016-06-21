<?php
	$id = $data['id'];
	$list = $data['list'];
	$limit = (isset($data['limit'])) ? $data['limit'] : 5;
	$show_comment_count = false;
	
	$widgets = array();
	
	foreach($list as $widget)
	{
		$posts = _exp_get_stories_by_type($widget['type'], $limit);
		if(is_array($posts) && !empty($posts) && sizeof($posts) > 0)
		{
			$widget['posts'] = $posts;
			$widgets[] = $widget;
		}
	}
?>

<?php 
	if(!empty($widgets) && sizeof($widgets) > 0) :
?>

<div id="TabbedWidget<?php echo $id;?>" class="widget tab-widget clearfix">
	
   <div class="tabs-container clearfix"> 
    <ul class="tabs">
		<?php 
			foreach($widgets as $key=>$widget): ?>
				<li><a href="#" <?php if($key==0)echo 'class="current"';?>><?php echo $widget['title'];?></a></li>
		<?php endforeach;?>
	</ul>
	<div class="tabs-content-group">
		<?php 
			foreach($widgets as $key=>$widget) :
		?>	
				<div class="tabs-content<?php if($key==0)echo ' tabs-content-visible';?>">
					<?php 
						
						if($widget['type'] == "mostcommented")
						{
							$show_comment_count = true;
						}
					
						$posts = $widget['posts'];
					?>
					<div class="content">
					<?php exp_display_story_links_widget($posts, $show_comment_count); ?>
					Over the past three days
					</div>
				</div>
		<?php endforeach; ?>
	</div>
 </div>
</div>

<?php endif;?>