<?php

class VM_manager
{
	const KEY_DEFAULT_VIDEO = '_vm_default_video';
	const JW_API_KEY = 'gnR3yuuf';
	const JW_API_SECRET = '1fE8Iu03ITmGVettLX5awiFo';
	const YT_API_KEY = 'AIzaSyCUqBfnlTmFfOAwlK-3waoRdwZ061DsJho';

	private $post_id;
	
	public $videos;
	public $default_video;

	public function __construct($post_id, $load_data = true)
	{
		$this->post_id       = $post_id;
		$this->default_video = $this->_get_default();
		if( $load_data ) $this->_load();
	}

	public function add($video_url)
	{
	  if( false == $this->post_id ) return 'A video can only be attached to a post!';
     
		if  (strpos($video_url, 'youtube.com') > 0)  {

		 $video_url_array = array();
		 parse_str(parse_url($video_url, PHP_URL_QUERY), $video_url_array);
		 $video_id = $video_url_array['v'];

		 $yt_api_url = "https://www.googleapis.com/youtube/v3/videos?id=".$video_id."&key=".self::YT_API_KEY."&part=snippet";
 
         $error_message = 'Please provide a valid youtube video url';
         $video_raw_details = $this->_getApi($yt_api_url);
         
         if(empty( $video_raw_details) ) return $error_message . $yt_api_url;
         $video_details_array = json_decode($video_raw_details);
         if(empty( $video_details_array) ) return $error_message;

         $video_details = $video_details_array->items[0];

        if( false == $video_details ) return $error_message;

         $url         = $video_url;
         $embed_url   = "//www.youtube.com/embed/$video_id";
         $thumb       = array( 'url' => esc_html($video_details->snippet->thumbnails->medium->url), 'width' => 200, 'height' => 150 );

         $title       = esc_html( $video_details->snippet->title );
         $caption     = '';
         $description = esc_html( $video_details->snippet->description );
      
      }elseif (strpos($video_url, 'content.jwplatform.com') > 0) {  // Code for JW Player
             
         $jw_helper = new BotrAPI(self::JW_API_KEY, self::JW_API_SECRET);
         $video_id = @array_shift(explode("-", pathinfo($video_url, PATHINFO_FILENAME)));
         $jw_response = $jw_helper->call("/videos/show",array('video_key'=>$video_id));

         if ( (empty($jw_response)) || ($jw_response['status'] == "error")) return 'Please provide a valid jwplatform video url';
         
         $url         = $video_url;
         $embed_url   = $video_url;
         $thumb_pic       = "http://content.bitsontherun.com/thumbs/{$video_id}-380.jpg";
         $caption     = '';
         $title       = esc_html( $jw_response['video']['title'] );
         $description = esc_html( $jw_response['video']['description'] );
         $thumb = array( 'url' => $thumb_pic, 'width' => 200, 'height' => 150 );

      } elseif (strpos($video_url, 'vimeo.com') > 0) { // Code for vimeo
         
         $error_message = 'Please provide a valid vimeo video url';
         $video_id = (int) substr(parse_url($video_url, PHP_URL_PATH), 1);
         if($video_id == 0 ) return $error_message;
         $video_raw_details = $this->_getApi("http://vimeo.com/api/v2/video/$video_id.php");
         if(empty( $video_raw_details) ) return $error_message;
         
         $video_details_array = unserialize($video_raw_details);
         if(empty( $video_details_array) ) return $error_message;
         
         $video_details = $video_details_array[0];
         if(empty( $video_details ) ) return $error_message;
         
         $url         = $video_details['url'];
         
         $thumb = array( 'url' => $video_details['thumbnail_medium'], 'width' => 200, 'height' => 150 );
         
         $thumb_size = getimagesize($thumb['url']);
         if( $thumb_size && !empty($thumb_size) )
         {
            $thumb['width']  = $thumb_size[0];
            $thumb['height'] = $thumb_size[1];
         }     
         
         $title       = esc_html( $video_details['title'] );
         $caption     = '';
         $description = esc_html( $video_details['description'] );
         
         $embed_url = null;
         
      } elseif (strpos($video_url, 'dailymotion.com') > 0) {  // Code for dailymotion   
         
         $error_message = 'Please provide a valid Dailymotion video url';
         $video_id = basename($video_url);
         if(strlen($video_id) == 0 ) return $error_message;     

         $video_raw_details = $this->_getApi("https://api.dailymotion.com/video/$video_id?fields=thumbnail_url,title,description,embed_url");
         if(empty( $video_raw_details) ) return $error_message;
         
         $video_details =  json_decode($video_raw_details, TRUE);
         if(empty( $video_details) ) return $error_message;
                 
         $url = esc_html( $video_details['embed_url'] );
         $thumb = array( 'url' => $video_details['thumbnail_url'], 'width' => 200, 'height' => 150 );
         
         $thumb_size = getimagesize($thumb['url']);
         if( $thumb_size && !empty($thumb_size) )
         {
            $thumb['width']  = $thumb_size[0];
            $thumb['height'] = $thumb_size[1];
         }     
         
         $title       = esc_html( $video_details['title'] );
         $caption     = '';
         $description = esc_html( $video_details['description'] );
         $embed_url = esc_html( $video_details['embed_url'] );
         
      } elseif (strpos($video_url, 'facebook.com') > 0) {
      		
      		$video_id = parse_url($video_url);
			parse_str($video_id['query']);
			if (!$v)
			{
				$str = explode("/", $video_url);
				$v = $str[count($str)-2];
			}
			$video_id =$v;

			if (strlen($video_id) == 0 ) return 'Please provide a valid Facebook video url';

            $thumb = array( 'url' => "https://graph.facebook.com/".$video_id."/picture", 'width' => 200, 'height' => 150 );

            $title       = "Facebook Video";
            $caption     = '';
            $description = "Facerbook video description";
            $embed_url = $video_url;
            $url = $video_url;
      } else return 'Please provide a valid Youtube/Vimeo/Dailymotion/Facebook/jwplatform video url';
      
		// we got here means everything is ok						
      $thumb  = new VM_thumbnail( $thumb['url'], $thumb['width'], $thumb['height'] );
      
		// Construct the attachment array
		$attachment = array(
			'post_mime_type' => 'video/mpeg',
			'guid'           => $url,
			'post_parent'    => $post_id,
			'post_title'     => $title,
			'post_content'   => $description,
		);
      
		// Save the data in wpdb
		$video_id = wp_insert_attachment( $attachment, false, $this->post_id );
		if ( ! is_wp_error( $video_id ) )
		{
			$video = new VM_video( $video_id, $this->post_id, $title, $caption, $description, $url, $youtube_url, $thumb );

			$metadata = array(
				'url'         => $video->url,
				'youtube_url' => $video->youtube_url,

				'thumbnail'   => array(
					'url' => $video->thumbnail->url,
					'width' => $video->thumbnail->width,
					'height' => $video->thumbnail->height
				)
			);
			
			wp_update_attachment_metadata( $video->id, $metadata );

			// if there is no default video set, set this one as the default video
			if( false == $this->default_video ) $this->set_default( $video );
		}
		else
		{
			$video = 'The video could not be added. Please try again!';
		}

		return $video;
	}

	public function delete($video_or_id)
	{
		if( false == ( $video_or_id instanceof VM_video ) ) $video_or_id = new VM_video( $video_or_id );

		if( false == $this->has( $video_or_id ) ) return false;

		if( false == $video_or_id->delete() ) return false;

		// if this is the default video, unset it and set the latest video as the default one
		if( $this->is_default( $video_or_id ) )
		{
			// set the latest video as the default one
			if( ! is_array( $this->videos ) || count( $this->videos ) < 1 ) $this->_load();

			$this->set_default( $this->videos[0] );
		}

		return true;
	}

	// is the passed video a child of this post
	public function has($video_or_id)
	{
		if( false == $this->post_id ) return false;

		if( false == ( $video_or_id instanceof VM_video ) ) $video_or_id = new VM_video( $video_or_id );

		return ( $video_or_id->parent_id == $this->post_id );
	}

	public function set_default($video_or_id)
	{
		if( false == $this->post_id || false == $video_or_id ) return false;

		$video_id = ( $video_or_id instanceof VM_video ) ? $video_or_id->id : $video_or_id;

		if( ! update_post_meta( $this->post_id, self::KEY_DEFAULT_VIDEO, $video_id ) )
		{
			add_post_meta( $this->post_id, self::KEY_DEFAULT_VIDEO, $video_id, true );
		}

		if( $video_or_id instanceof VM_video )
			$this->default_video = $video_or_id;
		else
			$this->default_video = new VM_video( $video_id );

		return true;
	}

	public function is_default($video_or_id)
	{
		$video_id = ( $video_or_id instanceof VM_video ) ? $video_or_id->id : $video_or_id;

		return ( $video_id == $this->default_video->id );
	}

	public static function get_latest_by_category($category_id = 0, $page_num = 0, $num_videos = 10)
	{
		global $wpdb;

		// sanitize params
		$category_id = (int) $category_id;
		$page_num    = (int) $page_num;
		$num_videos  = (int) $num_videos;
		$offset      = $page_num * $num_videos;

		$sql = "SELECT v.ID, v.post_title, v.post_parent, m.meta_value
					FROM {$wpdb->posts} AS v
						 INNER JOIN {$wpdb->posts} AS p ON v.post_parent = p.ID
						 INNER JOIN {$wpdb->postmeta} AS m ON v.ID = m.post_id ";

		if( $category_id )
		{
			$sql .= " INNER JOIN wp_term_relationships AS tr ON v.post_parent = tr.object_id
						 INNER JOIN wp_term_taxonomy AS tt on tr.term_taxonomy_id = tt.term_taxonomy_id 
								AND tt.term_id = $category_id
								AND tt.taxonomy = 'category' ";
		}

		$sql .= "
				 WHERE p.post_status = 'publish'
					AND v.post_mime_type LIKE 'video%'
					AND m.meta_key = '_wp_attachment_metadata'
				 ORDER BY v.post_date DESC";

		// if the user has asked the result set to be limited
		if( $num_videos )
		{
			$sql .= "
				 LIMIT $offset, $num_videos";
		}
		
		$post_videos = $wpdb->get_results( $sql );
		$videos      = array();

		foreach( (array)$post_videos as $video )
		{
			$metadata  = maybe_unserialize( $video->meta_value );
			$thumbnail = new VM_thumbnail( $metadata['thumbnail']['url'], $metadata['thumbnail']['width'], 
					  $metadata['thumbnail']['height'] );
			
			$videos[] = new VM_video($video->ID, $video->post_parent, $video->post_title, $video->post_excerpt,
					  $video->post_content, $metadata['url'], $metadata['youtube_url'], $thumbnail );
		}

		return $videos;
	}

	public static function get_count($category_id = 0)
	{
		global $wpdb;

		$category_id = (int)$category_id;

		$sql = "SELECT COUNT(v.ID)
					FROM {$wpdb->posts} AS v
						 INNER JOIN {$wpdb->posts} AS p ON v.post_parent = p.ID";

		if( $category_id )
		{
			$sql .= " INNER JOIN wp_term_relationships AS tr ON v.post_parent = tr.object_id
						 INNER JOIN wp_term_taxonomy AS tt on tr.term_taxonomy_id = tt.term_taxonomy_id
								AND tt.term_id = $category_id
								AND tt.taxonomy = 'category' ";
		}

		$sql .= "
				 WHERE p.post_status = 'publish'
					AND v.post_type = 'attachment'
					AND v.post_mime_type LIKE 'video%'";

		return $wpdb->get_var( $sql );
	}

	public static function trim_content($content, $length)
	{
		if( ! is_string( $content ) ) return false;

		if( ! is_numeric( $length ) ) return false;

		$content = trim( $content );

		// Limit the post by wordwarp to check for more tag
		$content = wordwrap( strip_tags( $content ), $length, "[lpa]" );
		$token_position = strpos( $content, '[lpa]' );

		return ( $token_position === false ) ? $content : substr( $content, 0, $token_position ) . '...';
	}

	public static function word_break($text, $break_index = 12)
	{
		$count   = 0;
		$output  = '';
		$and_shy = "&shy;";

		if( ! empty( $text ) && $text != '' )
		{
			$words_array = explode( ' ', $text );

			foreach( $words_array as &$word )
			{
				$word        = trim( $word );
				$word_length = strlen( $word );

				if( $word_length >= $break_index ) $word = wordwrap( $word, $break_index - 4, $and_shy, true );
			}

			$output = implode( ' ', $words_array );
		}

		return $output;
	}

	private function _getApi($api_url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		$results = curl_exec($ch);
		curl_close($ch);
		return $results; 
	}

	private function _load()
	{
		if( false == $this->post_id ) return false;

		$args = 'post_parent=' . $this->post_id . '&post_type=attachment&post_mime_type=video&orderby=date&order=DESC';
		
		$post_videos = get_children( $args );
		
		foreach( (array)$post_videos as $vid )
		{
			$this->videos[] = new VM_video($vid->ID, $this->post_id, $vid->post_title, $vid->post_excerpt, $vid->post_content );
		}

		return true;
	}

	private function _get_default()
	{
		if( false == $this->post_id ) return false;

		if( false == ( $default_video_id = get_post_meta( $this->post_id, self::KEY_DEFAULT_VIDEO, true ) ) )
		{
			// if there is no default video set, set the latest one as the default video and return it
			$this->_load();

			if( is_array( $this->videos ) && count( $this->videos ) > 0 )
			{
				$this->set_default( $this->videos[0] );

				return $this->videos[0];
			}
			else // there are no vidoes associated with this post so returning false
			{
				return false;
			}
		}

		return new VM_video( $default_video_id );
	}
}
