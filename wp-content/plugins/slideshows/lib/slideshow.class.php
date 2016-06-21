<?php
/**
 * This class holds all the information about a slideshow such as title, content, default_image, images, etc.
 *
 * @author amjad.sheikh
 */

class SS_slideshow
{
	public $id;

	public $title;
	public $description;
	public $short_description;
	public $comment_count;
	public $date_gmt;

	public $default_image;
	public $images;
	public $date;

	public $permalink;

	public function __construct( $post_or_id )
	{
		$post = is_numeric( $post_or_id ) ? get_post( $post_or_id ) : $post_or_id;

		$this->id                = $post->ID;
		$this->title             = esc_html($post->post_title);
		$this->description       = $post->post_content;
		$this->short_description = esc_html($post->post_excerpt);
		$this->comment_count     = $post->comment_count;
		$this->date_gmt          = date('Y-m-d\TH:i:s \G\M\T', strtotime( $post->post_date_gmt ) );

		$this->date              = human_time_diff( strtotime( $post->post_date ) );
		$this->permalink         = get_permalink( $this->id );
				  
		$manager             = new IM_manager( $this->id );
		$this->default_image = $manager->default_image;
		$this->images        = $manager->images;
	}
}