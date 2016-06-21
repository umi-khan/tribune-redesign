<?php

class Youtube_helper
{
	const BATCH_REQUEST_URI = 'http://gdata.youtube.com/feeds/api/videos/batch';

	const XMLNS_ATOM_URI = 'http://www.w3.org/2005/Atom';
	const XMLNS_BATCH_URI = 'http://schemas.google.com/gdata/batch';
	const XMLNS_MEDIA_URI = 'http://search.yahoo.com/mrss/';
	const XMLNS_GD_URI = 'http://schemas.google.com/g/2005';
	const XMLNS_YT_URI = 'http://gdata.youtube.com/schemas/2007';

	const RESPONSE_CODE_OK = 200;
	const RESPONSE_CODE_VIDEO_NOTFOUND = 404;

	public function search_by_video_url($video_urls = array())
	{
		if( ! is_array( $video_urls ) && ! empty( $video_urls ) )
		{
			$video_urls = (array) $video_urls;
		}

		$video_ids = array();
		foreach( $video_urls as $url )
		{
			if( ( $vid = $this->_get_videoid_from_url( $url ) ) ) $video_ids[] = $vid;
		}

		return $this->search_by_videoid( $video_ids );
	}

	public function search_by_videoid($video_ids = array())
	{
		if( ! is_array( $video_ids ) && ! empty( $video_ids ) )
		{
			$video_ids = (array) $video_ids;
		}

		if( count( $video_ids ) < 1 )
			return false;

		$payload =
			'<feed
				xmlns="'			. self::XMLNS_ATOM_URI	. '"
				xmlns:media="' . self::XMLNS_MEDIA_URI . '"
				xmlns:batch="' . self::XMLNS_BATCH_URI . '"
				xmlns:yt="'		. self::XMLNS_YT_URI		. '">

				<batch:operation type="query"/>';

		foreach( $video_ids as $video_id )
		{
			$payload .= '
				<entry>
					<id>http://gdata.youtube.com/feeds/api/videos/' . $video_id . '</id>
				</entry>';
		}

		$payload .= '
			</feed>';

		if( ! ( $response = $this->_post_payload( $payload ) ) )
			return false;

		return $this->_parse_response( $response );
	}

	private function _post_payload($payload)
	{
		$request = _wp_http_get_object();
		$response = $request->request( self::BATCH_REQUEST_URI, array( 'method' => 'POST', 'body' => $payload ) );

		if( ! is_array( $response ) || ! is_array( $response['response'] ) || ( $response['response']['code'] != 200 ) )
		{
			return false;
		}

		return $response['body'];
	}

	private function _parse_response($response)
	{
		if( ! class_exists( 'SimpleXMLElement' ) )
			return false;

		$xml = new SimpleXMLElement( $response );

		$feed = $xml->children( self::XMLNS_ATOM_URI );

		if( count( $feed ) < 1 )
			return false;

		$entries = array();

		foreach( $feed->entry as $entry )
		{
			$batch = $entry->children( self::XMLNS_BATCH_URI );

			if( count( $batch ) < 1 ) continue;

			$batch_attributes = $batch->status->attributes();

			if($batch_attributes['code'] != self::RESPONSE_CODE_OK)
			{
				$error = new Youtube_error();
				
				$error->id      = $this->_parse_entry_id( (string) $entry->id );
				$error->code    = (string) $batch_attributes['code'];
				$error->message = (string) $batch_attributes['reason'];

				$entries["$error->id"] = $error;

				continue;
			}

			$media = $entry->children( self::XMLNS_MEDIA_URI );

			if( count( $media ) < 1 ) continue;

			$video = new Youtube_video();

			$video->id        = $this->_parse_entry_id( (string) $entry->id );
			$video->title     = (string) $media->group->title;
			$video->published = (string) $entry->published;
			$video->updated   = (string) $entry->updated;
			$video->category  = (array) explode( ',', (string) $media->group->category );

			foreach( $media->group->content as $content )
			{
				$attributes = $content->attributes();

				$entry_content = array(
					'url'       => (string) $attributes['url'],
					'type'      => (string) $attributes['type'],
					'medium'    => (string) $attributes['medium'],
					'isDefault' => (string) $attributes['isDefault'],
					'duration'  => (string) $attributes['duration']
				);

				if( $attributes['isDefault'] )
				{
					$video->duration = array(
						'value' => (string)$attributes['duration'],
						'unit'  => 'seconds'
					);

					$video->url = $entry_content['url'];
				}

				$video->content[] = $entry_content;
			}

			$video->decription = (string) $media->group->description;
			$video->tags       = (array) explode( ',', (string) $media->group->keywords );

			$player = $media->group->player->attributes();
			$video->player = (string) $player['url'];

			foreach( $media->group->thumbnail as $thumbnail )
			{
				$attributes = $thumbnail->attributes();

				$entry_thumbnail = array(
					'url'    => (string) $attributes['url'],
					'height' => (string) $attributes['height'],
					'width'  => (string) $attributes['width']
				);

				$video->thumbnails[] = $entry_thumbnail;
			}

			// setup the default thumbnail
			$default_thumb        = $video->thumbnails[0];
			$default_thumb['url'] = substr( $default_thumb['url'], 0, strrpos( $default_thumb['url'], '/' ) + 1 ) . 'default.jpg';
			
			$video->thumbnails['default']  = $default_thumb;

			$video->author = array(
				'name' => (string) $entry->author->name,
				'uri'  => (string) $entry->author->uri
			);

			$gd = $entry->children( self::XMLNS_GD_URI );

			if( count( $gd ) > 0 )
			{
				if( count( $gd->rating ) > 0 )
				{
					$rating = $gd->rating->attributes();
					$video->rating = array(
						'average'   => (string) $rating['average'],
						'max'       => (string) $rating['max'],
						'min'       => (string) $rating['min'],
						'numRaters' => (string) $rating['numRaters']
					);
				}

				if( count( $gd->comments ) > 0 && count( $gd->comments->feedLink ) > 0 )
				{
					$comments = $gd->comments->feedLink->attributes();
					$video->comments = array(
						'uri'   => (string) $comments['href'],
						'count' => (string) $comments['countHint']
					);
				}
			}

			$yt = $entry->children( self::XMLNS_YT_URI );

			if( count( $yt ) > 0 && count( $yt->statistics ) > 0 )
			{
				$stats = $yt->statistics->attributes();
				$video->statistics = array(
					'favoriteCount' => (string) $stats['favoriteCount'],
					'viewCount'     => (string) $stats['viewCount']
				);
			}
		}

		$entries["$video->id"] = $video;

		return ( count( $entries ) < 1 ) ? false : $entries;
	}

	private function _parse_entry_id($id)
	{
		if( empty( $id ) )
			return false;

		// id is in the format http://gdata.youtube.com/feeds/api/videos/{video_id}
		return substr( $id, strrpos( $id, '/' ) + 1 );
	}

	private function _get_videoid_from_url($url)
	{
		$protocol = '(http://)|(http://www.)|(www.)';
		// escape those reg exp characters
		$protocol = str_replace( '.', '\.', str_replace( '/', '\/', $protocol ) );
		 //if empty arg passed, let it it match anything at beginning
		$protocol = ( $protocol != '' ) ? '^(' . $protocol . ')' : $protocol;
		//build the match string
		$match_str = '/' . $protocol . 'youtube\.com\/(.+)(v=.+)/';

		// find the matches and put them in $matches variable
		preg_match( $match_str, $url, $matches );

		if( $matches != null )
		{
			if( count( $matches ) >= 3 )
			{
				//the last match will be the querystring - split them at amperstands
				$qs = explode( '&', $matches[count( $matches ) - 1] );

				//loop through the params
				for( $i = 0; $i < count( $qs ); $i++ )
				{
					//split at = to find key/value pairs
					$x = explode( '=', $qs[$i] );
					//if the param is 'v', and it has a value associated, we want it
					if( $x[0] == 'v' && $x[1] ) return $x[1];
					else return false;
				}
			}
		}

		return false;
	}
}

class Youtube_video
{
	public $id;
	public $published;
	public $updated;

	public $url;

	public $category;
	public $content;
	public $decription;
	public $tags;
	public $player;
	public $thumbnails;
	public $title;

	public $duration;

	public $statistics;

	public $author;

	public $comments;
}

class Youtube_error
{
	public $code;
	public $message;
}