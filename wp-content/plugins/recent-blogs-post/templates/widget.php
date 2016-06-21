	<div class="more-story blogs">
 		<h4 class="top-news"><span><?php echo $widget_title;?></span></h4>
		<?php
			$counter = 0;
			$posts_count = count( $posts );
			if ($posts_count > 0 ){
			foreach( $posts as $post ) :
				$media = $post['media'];
				$author = $post['author'];

				?>

	<div class="story col-xs-12 col-lg-3 small-story <?php if($counter==0){echo 'first';}elseif($counter==$posts_count-1){echo 'last';} ?>">
	<div class="<?php // if($counter!=0){echo 'col-xs-6 col-lg-12';}?>" <?php // if($counter!=0){echo 'style="padding:0;"';}?>>
	<a href="<?php echo $post['url']; ?>" class="image">
				<img width="125" height="94" alt="<?php echo $media['caption'];?>" title="<?php echo $media['caption'];?>" src="<?php echo $media['thumb_url'];?>">
			</a>
			</div>
		<div class="content <?php // if($counter!=0){echo 'col-xs-6 col-lg-12';}?>" <?php if($counter!=0){echo 'style="padding:0;"';}?> clearfix">
			
			<h2 class="title">
				<a title="<?php echo $post['title'];?>" href="<?php echo $post['url'];?>"><?php echo $post['title'];?></a>
			</h2>
			<!--<div class="meta">
				<span class="author"><?php echo $author['name'];?></span>
			</div>-->
		</div>

	</div>

	<?php 
	$counter++;
	endforeach;
	} ?>
</div>
 <div class="clear"></div>
