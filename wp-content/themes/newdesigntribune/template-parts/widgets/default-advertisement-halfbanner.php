<div class="widget">
	<div class="ad-halfbanner-container">
		<div id="ad-halfbanner-right" class="ad-halfbanner"></div>
	</div>
</div>

<?php
add_action('exp_load_ads','exp_show_halfbanner_ad');

if( false == function_exists( 'exp_show_halfbanner_ad' ) )
{
	function exp_show_halfbanner_ad()
	{
		?>
		<div class="ad-loader for-ad-halfbanner-right">
			<!-- Tribune_All_Right_234x60 -->
				<script type='text/javascript'>
					GA_googleFillSlot("Tribune_All_Right_234x60");
				</script>
		</div>
		<?php
	}
}

?>



