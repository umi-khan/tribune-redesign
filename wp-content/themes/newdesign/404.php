<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>
<div class="col-lg-8">
<div id="content" class="span-24 error-page">
    <div class="primary span-16">
		  <img class="error-img" src="<?php echo get_template_directory_uri().'/img/404.jpg?v=0.1'; ?>" border="0" alt="" width="279" height="172" />
        <h2>Something went wrong, this page doesn't exist.</h2>
        <div class="content">
			  <p>
				  We're sorry, the page you've requested does not exist at this address. Please note:
					<br/>
					If you typed in the address, used a bookmark or followed a link from another Web site, the page is no longer available.
			  </p>
			   <form action="/" method="get" id="cfct-search">
	<input type="hidden" name="cx" value="partner-pub-2620341023138785:7641568038" />
	<input type="hidden" name="cof" value="FORID:10" />
	<input type="hidden" name="ie" value="UTF-8" />
	<input type="hidden" name="s" value="true" />
	<label class="label" for="google-search-textbox">Search</label>
	<input type="text" class="text" id="google-search-textbox" name="q" size="17" />
	<input class="form-submit submit" name="sa" type="submit" value="Search" />
</form>

<script type="text/javascript" src="http://www.google.com.pk/coop/cse/brand?form=cse-search-box&amp;lang=en"></script>
        </div>
    </div>
</div><!--#content-->
</div>
 <div class="col-lg-4">
<?php dynamic_sidebar('sidebar-3'); ?>

</div>
</div>
<?php get_footer(); ?>
