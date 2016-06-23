<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>
<style>
.group .first{min-height:auto;}
</style>
<?php $url = $_SERVER["REQUEST_URI"]; ?>
<div class="<?php if (strpos($url, 'slideshows') !== false) {echo 'col-lg-12';}else{echo 'col-lg-8';} ?>">
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
	<?php exp_primary();?>

	</main><!-- .site-main -->


</div><!-- .content-area -->
<div class="col-lg-4">
<?php dynamic_sidebar('sidebar-3'); ?>

</div>
	</div>
<?php get_footer(); ?>
