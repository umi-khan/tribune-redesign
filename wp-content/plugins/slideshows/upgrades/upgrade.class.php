<?php
/**
 * This class will convert post types of all old slideshows in the slideshow category to slideshow
 *
 * @author amjad.sheikh
 */
		
class SS_upgrade
{
	private $_post_type;
	private $_category_from;
	private $_num_slideshows;

	public function  __construct()
	{
		$this->_post_type      = SS_manager::TYPE_NAME;
		$this->_category_from  = get_cat_ID( 'slideshows' ) ;
		$this->_num_slideshows = $this->get_slideshows_count();
	}

	public function import_slideshows()
	{
		if( $this->_num_slideshows < 1 ) return true;

		print 'Number of slideshows to import: ' . $this->_num_slideshows . '<br />';

		$old_slideshows = get_posts( array( 'numberposts' => $this->_num_slideshows, 'category'=> $this->_category_from, 'post_status'=>'any' ) );

		print '<pre>' . print_r( $old_slideshows, true ) . '</pre>';
		
		foreach( (array)$old_slideshows as $slideshow )
		{
			$this->import_slideshow_images( $slideshow->ID );

			$slideshow->post_type     = $this->_post_type;
			$slideshow->post_category = $this->get_slideshow_categories( $slideshow->ID );
			print '<pre>categories of slideshow: ' . $slideshow->ID . print_r( $slideshow->post_category, true ) . '</pre>';
			
			wp_update_post( $slideshow );
		}
		
		return true;
	}

	private function import_slideshow_images( $slideshow_id )
	{
		$images = $this->get_slideshow_images( $slideshow_id );

		print '<pre>images of slideshow: ' . $slideshow_id . print_r( $images, true ) . '</pre>';

		if( empty( $images ) ) return;
		
		$manager = new IM_manager( $slideshow_id );

		foreach( $images as $image )
		{
			$new_image = $manager->add( $image->image_id );

			$new_image->title   = $image->title;
			$new_image->caption = $image->caption;

			$new_image->save();
		}

		return true;
	}

	private function get_slideshow_images( $slideshow_id )
	{
		global $wpdb;

		$table = $wpdb->prefix . 'nip_images';

		return $wpdb->get_results( "SELECT * FROM $table WHERE post_id = $slideshow_id" );
	}

	private function get_slideshows_count()
	{
		return get_category( $this->_category_from )->count;
	}

	private function get_slideshow_categories( $slideshow_id )
	{
		global $wpdb;
		$table  = $wpdb->prefix . 'nip_slideshows';

		return $wpdb->get_col( "SELECT category_id FROM $table WHERE post_id = $slideshow_id" );
	}
}
