<!doctype html>
<html amp>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
	<?php do_action( 'amp_post_template_head', $this ); ?>

	<style amp-custom>
	<?php $this->load_parts( array( 'style' ) ); ?>
	<?php do_action( 'amp_post_template_css', $this ); ?>
	</style>
</head>
<body>
<nav class="amp-wp-title-bar">
	<div>
		<a href="<?php echo esc_url( $this->get( 'home_url' ) ); ?>">
			<?php $site_icon_url = 'http://i1.tribune.com.pk/wp-content/uploads/2016/04/logo.gif'; ?>
			<?php if ( $site_icon_url ) : ?>
				<amp-img src="<?php echo esc_url( $site_icon_url ); ?>" width="202" height="60" class="amp-wp-site-icon"></amp-img>
			<?php endif; ?>
		</a>
	</div>
</nav>


<div class="amp-wp-content">
	<?php isa_amp_featured_img( 'full' );?>
	<h1 class="amp-wp-title"><?php echo wp_kses_data( $this->get( 'post_title' ) ); ?></h1>
	<ul class="amp-wp-meta">
		<?php $this->load_parts( apply_filters( 'amp_post_template_meta_parts', array( 'meta-author', 'meta-time', 'meta-taxonomy' ) ) ); ?>
	</ul>
	<?php echo $this->get( 'post_amp_content' ); // amphtml content; no kses ?>
</div>
<?php do_action( 'amp_post_template_footer', $this ); ?>
</body>
</html>
