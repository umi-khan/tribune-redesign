<div class="widget ad-box-container">
	<div id="ad-box-right" class="ad-box"></div>
</div>

<?php
add_action('exp_load_ads','exp_show_box_ad');

if( false == function_exists( 'exp_show_box_ad' ) )
{
	function exp_show_box_ad()
	{
		?>
		<div class="ad-loader for-ad-box-right">
			<!-- ca-pub-0361992696245770/Tribune_All_Right_300x250 -->
			<script type='text/javascript'> GA_googleFillSlot("Tribune_All_Right_300x250");</script>
		</div>
		<?php
	}
}

?>



