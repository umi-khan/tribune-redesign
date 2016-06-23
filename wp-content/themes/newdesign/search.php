<?php

// This file is part of the Carrington Blog Theme for WordPress
// http://carringtontheme.com
//
// Copyright (c) 2008-2009 Crowd Favorite, Ltd. All rights reserved.
// http://crowdfavorite.com
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) { die(); }

get_header();

?>

<div id="search" class="span-24">
	<h1 class="title">Search Results</h1>
	<div id="cse-search-results" class="span-16"></div>
		<script type="text/javascript">
        var googleSearchIframeName = "cse-search-results";
        var googleSearchFormName = "cse-search-box";
        var googleSearchFrameWidth = 900;
        var googleSearchResizeIframe = true;
        var googleSearchPath = "/cse";
        var googleSearchDomain = "www.google.com.pk";
		</script>
		<script type="text/javascript" src="http://www.google.com/afsonline/show_afs_search.js"></script>
</div>

<?php
get_footer();
?>