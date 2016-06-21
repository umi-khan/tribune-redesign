<?php

class VM_video
{
	public $id;
	public $parent_id;
	public $url;
	public $youtube_url;
	public $thumbnail;
	public $title;
	public $caption;
	public $description;

	public function  __construct($id, $parent_id=false, $title=false, $caption=false, $description=false, 
			  $url=false, $youtube_url=false, $thumb=false)
	{
		$this->id          = $id;

		if( false === $parent_id || false === $title || false === $caption )
		{
			$image = get_post( $id );
			
			$parent_id   = ( false === $parent_id ) ? $image->post_parent : $parent_id;
			$title       = ( false === $title ) ? $image->post_title : $title;
			$caption     = ( false === $caption ) ? $image->post_excerpt : $caption;
			$description = ( false === $description ) ? $image->post_content : $description;
		}

		$this->parent_id   = $parent_id;
		$this->title       = $title;
		$this->caption     = $caption;
		$this->description = $description;

		if( false === $url || false === $youtube_url || false === $thumb )
		{
			$metadata = wp_get_attachment_metadata( $this->id, true );

			$url         = ( false === $url ) ? $metadata['url'] : $url;
			$youtube_url = ( false === $youtube_url ) ? $metadata['youtube_url'] : $youtube_url;

			if( false == $thumb )
			{
				$thumb =  $metadata['thumbnail'];
				$thumb = new VM_thumbnail( $thumb['url'], $thumb['width'], $thumb['height'] );
			}
		}

		$this->url         = $url;
		$this->youtube_url = $youtube_url;
		$this->thumbnail   = $thumb;
	}

	public function player($width=640, $height=385)
	{
		$video = $this;
		
		include VM_PLUGIN_DIR . 'templates/video_player.php';
	}

	public function delete()
	{
		return wp_delete_attachment( $this->id );
	}

	public function save()
	{
		// update the image title and caption
		wp_update_post( array( 'ID' => $this->id, 'post_title' => $this->title, 'post_excerpt' => $this->caption ) );

		// update the attachment meta by adding the thumbnail size
		$metadata = array(
				'url'         => $this->url,
				'youtube_url' => $this->youtube_url,

				'thumbnail'   => array(
					'url' => $this->thumbnail->url,
					'width' => $this->thumbnail->width,
					'height' => $this->thumbnail->height
				)
			);
		wp_update_attachment_metadata( $this->id, $metadata );

		return true;
	}
}

class VM_thumbnail
{
	public $url;
	public $width;
	public $height;

	public function  __construct($url, $width, $height)
	{
		$this->url     = $url;
		$this->width   = $width;
		$this->height  = $height;
	}
}