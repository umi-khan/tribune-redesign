<?php if( false == isset( $sub_stories_heading ) ) $sub_stories_heading = 'Top News'; ?>

<div class="main group clearfix">
	<div class="col-lg-6 first top-news">
		<h4><?php echo $sub_stories_heading; ?></h4>
		<?php
			$args = array();
			$args['show_excerpt'] = false;
			$sub_story_count = ((count( $sub_stories )) > 4 ) ? 4 : count( $sub_stories );
			for ( $i = 0, $j = $sub_story_count; $i < $j; $i++ )
			{
				$args['is_first'] = ( $i == 0 ) ? true : false;
				$args['is_last']  = ( $i == $j - 1 ) ? true : false;

				if( $sub_stories[$i] instanceof IDisplayable_control ) $sub_stories[$i]->display( $args );
			}
		?>		
	</div>

	<div class="col-lg-6 last">
	<?php if( is_array( $top_story ) ) { 
		if (is_category()) {
			$cat = get_query_var('cat');
			$category_slug = get_category($cat);
		}
	?>
<div id="" class="story  picture-story clearfix">
	<div class="content">
  	<div class="cycle-slideshow gallery" 
	    data-cycle-fx="fade" 
	    data-cycle-slides="> div.slide"
	    data-cycle-timeout="5000"
	    >
 <?php
$loop = (count($top_story) > 5) ? 5 : count($top_story) ;
for ($i = 0; $i <= $loop; $i++) {
 	$style=($i==0)? "" : "display: none;" 
 	 	 		?>
	    <div class="slide <?php echo ( $top_story[$i]['video'] =="true" )? "video-pic" : "";?>" style="<?php echo $style?>">
			<a  href="<?php echo $top_story[$i]['link']; ?>">
				<?php echo ( $top_story[$i]['video'] =="true" )? "<span></span>" : "";?>
				<img src="<?php echo $top_story[$i]['img']; ?>" alt="<?php $top_story[$i]['caption']; ?>" width="100%" height="289" />
			</a>
			<h2 class="title"><a href="<?php echo $top_story[$i]['link']; ?>"><?php echo $top_story[$i]['title']; ?></a></h2>
	    </div>
<?php } ?>
       <div class="cycle-pager"></div>
	</div>
 	</div>
</div>
<?php }?>
	</div>
</div>