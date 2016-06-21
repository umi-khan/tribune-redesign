<div id="lm_layout_manager" class="lm_tabs_content clearfix">
	<ul id="lm_menu" class="menu expandfirst">
	<?php
	for( $i = 0, $j = count( $categories ); $i < $j; $i++ ):
		$cat = $categories[$i];
	?>
		<li>
			<a href="#" class="lm_section_link <?php if( $i == $j - 1 ) echo 'last'; ?> <?php echo $cat->slug; ?>"
				id="catid-<?php echo $cat->cat_ID; ?>">
				<?php echo $cat->name; ?>
			</a>
			<ul>
			<?php foreach( (array)$subcategories[$cat->cat_ID] as $sub ): ?>
				<li>
					<a href="#" class="lm_section_link" id="catid-<?php echo $sub->cat_ID; ?>"><?php echo $sub->name; ?></a>
				</li>
			<?php endforeach; ?>
			</ul>
		</li>
	<?php endfor; ?>
	</ul>

	<div id="lm_stories">
		<?php foreach( (array)$categories as $cat ): ?>
		 <div class="lm_section_content <?php if( $cat->cat_ID == 0 ) echo 'display-block'; ?>"
				id="stories-catid-<?php echo $cat->cat_ID; ?>">
			<div class="title"><?php echo $cat->name; ?></div>
			<?php
				foreach( $stories[$cat->cat_ID] as $story )
				{
					$story       = LM_story::get_story( $story );
					$category_id = $cat->cat_ID;
					
					include( $story_template );
				}
			?>
		 </div>

			<?php foreach( (array)$subcategories[$cat->cat_ID] as $subcat ) : ?>
				<div class="lm_section_content" id="stories-catid-<?php echo $subcat->cat_ID; ?>">
					<div class="title"><?php echo $subcat->name; ?></div>
					<?php
						foreach( (array)$stories[$subcat->cat_ID] as $story )
						{
							$story       = LM_story::get_story( $story );
							$category_id = $subcat->cat_ID;

							include( $story_template );
						}
					?>
				</div>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</div>
</div>