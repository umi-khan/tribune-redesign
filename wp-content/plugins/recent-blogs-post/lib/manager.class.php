<?php

Class Blog_Posts_Manager
{
	private $_url;
	private $_cache;
	private $_data;	
	private $_limit;

	function  __construct( $feed_url , $cache_time, $limit )
	{
		$this->_url   = $feed_url;
		$this->_limit = $limit;
		$this->_cache = new Blog_Posts_Cache( $this->_url, $cache_time );
	}

	public function get()
	{
		if ( false !== $this->_cache->is_expired() )
		{
			$this->_data = $this->_fetch_remote( $this->_url );
			$this->_cache->set( $this->_data );
		}
		else $this->_data = $this->_cache->get();
			
		return $this->_data;
	}

	private function _fetch_remote()
	{

		//$xml = simplexml_load_file( $this->_url );
		
		  $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $this->_url );
        $contents = curl_exec($c);
        curl_close($c);

$xml= simplexml_load_string($contents);

		
		
		if( false == $xml ) return false;

		if( !isset($xml->channel->item) && !is_array($xml->channel->item) ) return false;
				
		$data = array(
			'feeds_site_title' => (string)$xml->channel->title,
			'feeds_site_url'   => esc_url((string)$xml->channel->link),
			'feed_site_desc'   => esc_attr(strip_tags(@html_entity_decode((string)$xml->channel->description, ENT_QUOTES, get_option('blog_charset')))),
		);

		$items = $xml->channel->item;

		$limit       = intval($this->_limit);
		$posts  = array();

		for( $counter = 0; $counter < $limit; $counter++ )
		{
			$item    = $items[$counter];
			$creator = $item->children('http://purl.org/dc/elements/1.1/');
			$media = $item->children('http://search.yahoo.com/mrss/');
			$medium	 =  (string) $media->content->attributes()->medium;

			$posts[] = array(
				'title'  => (string) $item->title,
				'url'    => (string) $item->link,
				'media'	 => array(
					'caption' => (string) $media->content->description,
					'thumb_url'	  => (string) $media->content->thumbnail->attributes()->url,
					'large_url'	  => ($medium =='video') ? (string) $media->content->thumbnail->attributes()->url : (string) $media->content->attributes()->url
				 ),
				'author' => array(
					'name'      => (string) $creator->creator,
					'url'       => (string) $creator->creator_link,
					'photo_url' => (string) $creator->creator_photo
				)
			 );	
		}

		$data['posts'] = $posts;

		return $data;

	}
}