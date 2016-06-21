<?php

class LM_supercache extends LM_cache
{
	private $cache_path;
	private $supercache_enabled;

	public function __construct()
	{
		global $cache_path;
		$this->cache_path = $cache_path;

		global $super_cache_enabled;
		$this->supercache_enabled = $super_cache_enabled;
	}

	public function invalidate_file($page_uri)
	{
		$page_uri = $this->_sanitize_page_uri($page_uri);

		if($page_uri == '')
			return false;

		$this->_invalidate_cache_file($page_uri);
		
		if($this->supercache_enabled) $this->_invalidate_supercache_file($page_uri);
		
		return true;
	}

	private function _invalidate_supercache_file($page_uri)
	{
		$cache_dir = $this->cache_path . 'supercache/';
	
		if(!file_exists($cache_dir . $page_uri))
			return false;

		@unlink($cache_dir . $page_uri . 'index.html');
		@unlink($cache_dir . $page_uri . 'index.html.gz');
		prune_super_cache($cache_dir . $page_uri . 'page', true);
		@rmdir($cache_dir . $page_uri);

		return true;
	}

	private function _invalidate_cache_file($page_uri)
	{
		global $blog_cache_dir;
	
		if(($handle = @opendir($blog_cache_dir . 'meta/')) === false)
			return false;

		$cache_file_deleted = false;

		while(($file = readdir($handle)) !== false)
		{
			if(preg_match("/^$file_prefix.*\.meta/", $file))
			{
				$content_file = preg_replace("/meta$/", "html", $file);

				if(!($fsize = @filesize( $blog_cache_dir . $content_file )))
					continue; // .meta does not exists

				$meta = unserialize(file_get_contents($blog_cache_dir . 'meta/' . $file));
				if($page_uri != '' && $meta['uri'] == $page_uri)
				{
					@unlink($blog_cache_dir . 'meta/' . $file);
					@unlink($blog_cache_dir . $content_file);
					
					$cache_file_deleted = true;
					break;
				}
			}
		}
		
		closedir($handle);

		return $cache_file_deleted;
	}

	private function _sanitize_page_uri($page_uri)
	{
		if(strpos($page_uri, "://"))
		{
			$page_uri = substr($page_uri, strpos($page_uri, "://") + 3);
		}

		$page_uri = preg_replace("/(\?.*)?$/", '', $page_uri);
		$page_uri = str_replace('..', '', $page_uri);
		$page_uri = str_replace('/index.php', '/', $page_uri);
		$page_uri = preg_replace('/[ <>\'\"\r\n\t\(\)]/', '', $page_uri);
		$page_uri = trailingslashit(str_replace('\\', '', $page_uri));

		return $page_uri;
	}
}