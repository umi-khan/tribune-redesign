<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of cacheclass
 *
 * @author amjad.sheikh
 */
class Blog_Posts_Cache
{	
	private $_content = array();
	private $_filepath;
	private $_cache_time;
	private $_dir;
	private $_filename;

	public static function get_cache_dir()
	{
		return RECENTBLOGS_PLUGIN_DIR . 'cache/';
	}

	public function __construct( $feed_url, $cache_time )
	{
		$feed_url_array    = parse_url( $feed_url );

		$this->_dir        = sprintf( RECENTBLOGS_PLUGIN_DIR .'cache/%s' , $feed_url_array['host'] );
		$this->_filepath   = sprintf( $this->_dir .'/%s.txt' , ltrim( str_replace('/', '-', $feed_url_array['path'] ) , '-' )  );
		$this->_cache_time = $cache_time;
	}

	public function is_expired()
	{
		if(! file_exists( $this->_filepath ) )  return true;
		
		$minutes_last_updated = floor( ( time() - filemtime( $this->_filepath ) ) / 60 );
		return ( $minutes_last_updated > $this->_cache_time );
	}

	public function get()
	{
		$_content = file_get_contents( $this->_filepath );
		if ( !empty( $_content ) ) $this->_content = unserialize( $_content );
		return $this->_content;
	}

	public function set( $content )
	{		
		$this->_content = serialize( $content );

		if( false === is_dir( $this->_dir ) ) mkdir($this->_dir);
		
		$_tmp_file_path = str_replace( '.txt', '_'.microtime().'.tmp', $this->_filepath );

		$cachefp = fopen($_tmp_file_path, 'w');
		if( $cachefp )
		{
			fwrite( $cachefp, $this->_content );
			fclose( $cachefp );

			if( copy( $_tmp_file_path, $this->_filepath ) == true) unlink( $_tmp_file_path );
		}
	}
}
