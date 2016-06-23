<div class="widget ad-box-container">
	<div id="ad-box-right" class="ad-box"></div>
</div>

<?php
add_action('exp_load_ads','exp_show_box_ad');

if( false == function_exists( 'exp_show_box_ad' ) )
{
	function exp_show_box_ad()
	{
		
		$template_directory_uri = get_template_directory_uri();
		$current_section        = get_current_section( false );  ?>

		<div class="ad-loader for-ad-box-right">
			<script type='text/javascript'>
				<?php if( is_home() || is_tag() ) : ?>
				GA_googleFillSlot("Tribune_Home_300x250_Top");
				<?php elseif( $current_section == 'pakistan' ) : ?>
				GA_googleFillSlot("Tribune_Pakistan_300x250_Top");
				<?php elseif( $current_section == 'business' ) : ?>
				GA_googleFillSlot("Tribune_Business_300x250_Top");
				<?php elseif( $current_section == 'life-style' ) : ?>
				GA_googleFillSlot("Tribune_LifeStyle_300x250_Top");
				<?php elseif( is_single() ) : ?>
				GA_googleFillSlot("Tribune_Story_300x250_Top");
				<?php else : ?> 
				GA_googleFillSlot("Tribune_Default_300x250_Top");
				<?php endif; ?>
			</script>
		</div>
		<?php
	}
}

?>



