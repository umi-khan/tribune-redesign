<?php /* Template Name: Home1 */
// current category
$category_id = 0;

$layout = new LM_layout( $category_id, LM_config::GROUP_MAIN_STORIES );

?>

<?php get_header();  ?>
<div class="home">
<div class="col-lg-8">
<?php dynamic_sidebar( 'top-stories' ); ?>
</div>
<div class="group col-xs-12 col-lg-12">
<div style="width: 728px; margin:0 auto;">
<?php dynamic_sidebar( 'leaderboard-btf1' ); ?>
</div>
<?php dynamic_sidebar( 'middle-stories' ); ?>
<div class="group col-xs-12 col-lg-12">
<div style="width: 728px; margin:0 auto;">
<?php dynamic_sidebar( 'leaderboard-btf2' ); ?>
</div>
</div>
<?php dynamic_sidebar( 'middle-bottom-stories' ); ?>
  </div>
      <div class="col-lg-4 col-xs-12">
      <?php get_sidebar(); ?>
    </div>	
<?php dynamic_sidebar( 'bottom-stories' ); ?>
</div>
</div>
</div>
<?php get_footer(); ?>
