<?php

/**
 * This is an abstract class that has two purposes:
 * 1. act as a factory for creating the object of the correct cache class based on the plugin being enabled
 * 2. make sure that all the cache classes have an invalidate_file function
 *
 * Right now this class is present here only to support SuperCache plugin. Its not used with ExpressCache plugin.
 *
 * @package Layout_management
 */
abstract class LM_cache
{
	/**
	 * This function should be present in all implementations of the cache class, so that the client can call this 
	 * function in a polymorphic manner.
	 *
	 * @param string $page_uri The uri of the page that has to be purged
	 */
	abstract function invalidate_file($page_uri);

	/**
	 * This is a factory function which is responsible for instantiating the correct cache object.
	 * Right now it only return the SuperCache compliant cache object. Its not being used with ExpressCache.
	 *
	 * @global bool $super_cache_enabled
	 * @return bool|LM_supercache 
	 *
	 */
	public static function get_cache_obj()
	{
		if( false == defined( 'WP_CACHE' ) || false == WP_CACHE ) return;
		
		global $super_cache_enabled;
		if( $super_cache_enabled )
		{
			require_once LAYOUT_MANAGEMENT_PLUGIN_DIR . 'lib/supercache.class.php';
			return new LM_supercache();
		}

		return false;
	}
}