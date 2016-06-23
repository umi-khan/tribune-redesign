<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<link rel="shortcut icon" href="/favicon.ico"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php endif; ?>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js" type="text/javascript"></script>
	<?php
		wp_enqueue_script('jquery');
		wp_enqueue_script('google-search','http://cse.google.com.pk/coop/cse/brand?form=cse-search-box&lang=en',array(),'',true);
		//exp_insert_script($page_type);
	?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	
		<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentysixteen' ); ?></a>

		<header id="masthead" class="site-header" role="banner">
		<div class="site-inner">
			<div class="site-header-main">
			<div class="top-brand-bar">
			<?php if ( has_nav_menu( 'brand' ) ) : ?>
								<?php
									wp_nav_menu( array(
										'theme_location' => 'brand',
									 ) );
								?>
						<?php endif; ?>
						</div>
				<div class="site-branding">
					
					    <?php dynamic_sidebar( 'leaderboard-top' ); ?>
				</div>	
			</div>		
		</div>	
		</div>
			<div id="site-header-menu" class="site-header-menu">
				<div class="site-inner">
						<?php if ( has_nav_menu( 'primary' ) ) : ?>
							<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'twentysixteen' ); ?>">
							<div class="nav-icon">
								<span class="small-nav"><i class="fa fa-navicon"></i></span>
							</div>

							<div class="search-icon">
								<span class="small-search"><i class="fa fa-search"></i></span>
							</div>
								<?php exp_nav_menu(  array( 'theme_location' => 'main-menu', 'container' => '', 'depth' => 1, 'menu_id' => 'main-menu', 'menu_class' => 'primary-menu category') ); ?>
	
								<div id="sub-nav" class="span-24">
								<?php if( is_single() || is_category() ) : ?>
		<div class="span-14">

		<?php if (is_category('sports')):?>
				<?php get_control("trends"); ?>
			<?php else : ?>
			<?php exp_nav_menu(  array( 'theme_location' => 'main-menu', 'container' => '', 'menu_id' => 'sub-menu','menu_class' => $current_section.' subcategory', 'start_depth' => 1, 'depth' => 1)); ?>
		<?php endif; ?>
		</div>
								<?php else : ?>
								<!-- current trends -->
								<?php get_control("trends"); ?>
								<?php endif; ?>
							</nav><!-- .main-navigation -->
						<?php endif; ?>
							<div id="search">
								<form action="/" id="cse-search-box" method="get" class="search">
									<input type="hidden" name="cx" value="partner-pub-2620341023138785:7641568038" />
									<input type="hidden" name="cof" value="FORID:10" />
									<input type="hidden" name="ie" value="UTF-8" />
									<input type="hidden" name="s" value="true" />
									<input type="hidden" name="hq" value="more:recent4" />
									<input type="text" class="text" name="q" />
									<input type="image" src="<?php echo get_template_directory_uri() ?>/img/search-icon.gif" class="image" alt ="S" />
								</form>
							</div>
						</div>
					</div><!-- .site-header-main -->
			</header><!-- .site-header -->
		<div class="site-inner">
			<div id="content" class="site-content">


