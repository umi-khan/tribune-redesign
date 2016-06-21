<?php

	/*
	 * $lm_stories is a normal array of stories passed to this template file, this array containes objects of layoutmanagement.story class.
	 * All the stories in array will be shown here through a loop
	 */

if( is_array($lm_stories) && !empty($lm_stories) )	:		?>

	<div class="featured sidebar-right-child clear">

		<h3>Featured</h3>

<?php

	foreach($lm_stories as $key => $story) :
?>

		<div id="<?php echo $story->html_id; ?>" class="<?php echo $story->html_classes; ?>">

			<div class="featured-title">
				<a class="featured-link title" href="<?php echo $story->permalink;?>">
					<?php echo $story->title;?>
				</a>

            <div class="featured-author"><?php echo $story->author ?></div>

			</div>

			<div class="clear"></div>

		</div>

	<?php endforeach; ?>
	</div>
<?php endif; ?>