<?php
/*
Plugin Name: MailChimp Widget Sidebar
Plugin URI: https://express.pk
Description: MailChimp Widget for  Sidebar and single posts
Author: Express Media
Version: 1.0
Author URI: https://express.pk
*/
 
/**
 * Set up the autoloader.
 */

set_include_path(get_include_path() . PATH_SEPARATOR . realpath(dirname(__FILE__) . '/'));
spl_autoload_extensions('.class.php');
if (! function_exists('buffered_autoloader')) {
	function buffered_autoloader ($c) {
		try {
			spl_autoload($c);
		} catch (Exception $e) {
			$message = $e->getMessage();
			return $message;
		}
	}
}
spl_autoload_register('buffered_autoloader');
/**
 * Get the plugin object. All the bookkeeping and other setup stuff happens here.
 */
$ns_mc_plugin = NS_MC_Plugin::get_instance();
register_deactivation_hook(__FILE__, array(&$ns_mc_plugin, 'remove_options'));
?>
