<?php
	$id = $data['id'];
	$list = $data['list'];
	$limit = (isset($data['limit'])) ? $data['limit'] : 5;
	$show_title = ( count($list) == 1) ? false : true;
	
	$widgets = array();
	foreach($list as $widget)
	{
		if(!$show_title) $widget['title'] = 'Most '.$widget['title'];
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

<div id="TabbedWidget<?php echo $id;?>" class="most-popular widget tab-widget clearfix clear">
	
	<?php if($show_title) : ?>
		<h4>Most Popular</h4>
	<?php endif;?>
	
   <div class="tabs-container"> 
    <ul class="tabs clearfix">
		<?php 
			foreach($widgets as $key=>$widget): ?>
				<li><a href="#" <?php if($key==0)echo 'class="first current"';?>><?php echo $widget['title'];?></a></li>
		<?php endforeach;?>
	</ul>
	<div class="tabs-content-group">
		<?php 
			foreach($widgets as $key=>$widget) :
		?>	
				<div class="tabs-content<?php if($key==0)echo ' tabs-content-visible';?>">
					<?php 
						$show_comment_count = false;
						if($widget['type'] == "mostcommented")
						{
							$show_comment_count = true;
						}
					
						$posts = $widget['posts'];
					?>
					<div class="content">
					<?php
							exp_display_story_links_widget($posts, $show_comment_count);
					?>
					</div>
				</div>
		<?php endforeach; ?>
	</div>
 </div>
 <div class="note"> Over the past two days </div>
</div>

<?php endif;?>