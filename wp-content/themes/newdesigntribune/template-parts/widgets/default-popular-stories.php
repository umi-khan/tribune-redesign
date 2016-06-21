<?php
$stories = $data["stories"];
$excerpt_count = 100;
	
if (!empty($stories) && $stories != false) :
?>
	<div id="popular" class="widget">
		<h1 class="title">Most Popular</h1>
		<div class="content">
			<?php
			$stories_count = count($stories);
			$counter = 0;
			foreach($stories as $story):
				$story = LM_Story::get_story($story);
				$manager = new IM_Manager( $story->id );
				$image = $manager->default_image;
				$counter++;
				$class = ( $counter == $stories_count ) ? 'last' : '';
			?>
				<div id="<?php echo $story->html_id; ?>" class="story sub-story <?php echo $class;?>">
					<h2 class="title">
						<a href="<?php echo $story->permalink; ?>"><?php echo $story->title;?></a>
					</h2>
					<a href="<?php echo $story->permalink; ?>" class="story-image">
						<img src="<?php echo $image->thumbnail->url; ?>" alt="<?php $image->caption; ?>" width="100" height="75" />
					</a>
					<p class="excerpt"><?php echo $story->excerpt; ?></p>
					<div class="clearfix"></div>
				</div>
			<?php
			
			endforeach;
			?>
	
		</div>
	</div>
<?php endif; ?>