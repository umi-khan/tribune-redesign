<?php
/**
 * This class manages slideshows.
 *
 * @author amjad.sheikh
 */
class SS_manager
{
	const DEFAULT_PAGE_NUM = 1;
	const DEFAULT_LIMIT    = 8;
	const DEFAULT_CATEGORY = 0;
	const TYPE_NAME        = 'slideshow';
	const DEFAULT_INTERVAL = 2880;
	
	public static function get_latest($category = self::DEFAULT_CATEGORY, $page_num = self::DEFAULT_PAGE_NUM, 
			  $limit = self::DEFAULT_LIMIT)
	{
		global $wpdb;
		
		$category      = (int)$category;
		$limit         = (int)$limit;
		$offset        = (int)$page_num * $limit;
		$post_status   = 'publish';
		$order_results = true;

		$sql = self::_build_query( $category, $post_status, 'all', $order_results, $offset, $limit );

		$posts = $wpdb->get_results( $sql );
		
		$slideshows = array();
		foreach ( $posts as $post ) $slideshows[] = new SS_slideshow( $post );

		return $slideshows;
	}

	public static function get_mostviewed ( $category = self::DEFAULT_CATEGORY, $interval = self::DEFAULT_INTERVAL, $limit = self::DEFAULT_LIMIT )
	{
		$category = (int)$category;

		$interval = (int)$interval;
		$interval = ( false == $interval ) ? self::DEFAULT_INTERVAL : $interval;

		$limit    = (int)$limit;
		$limit    = ( false == $limit ) ? self::DEFAULT_LIMIT : $limit;

		$popular_slideshows = pp_get_popular_posts( $category, $limit, self::TYPE_NAME, $interval );
		
		$slideshows = array();
		foreach ( $popular_slideshows as $popular_slideshow ) $slideshows[] = new SS_slideshow( $popular_slideshow );

		return $slideshows;
	}
	
	public static function get_count($category = self::DEFAULT_CATEGORY)
	{
		global $wpdb;
		
		$category    = (int)$category;
		$post_status = 'publish';

		$sql = self::_build_query( $category, $post_status, 'count', false, false, false );
		
		return $wpdb->get_var( $sql );
	}

	private static function _build_query($category, $post_status = 'publish', $fields = 'all', $ordered = true,
			  $offset = 0, $limit = self::DEFAULT_LIMIT)
	{
		global $wpdb;

		$post_type = self::TYPE_NAME;

		switch( $fields )
		{
			case 'all' :
				$fields = "p.*";
				break;

			case 'count' :
				$fields = "COUNT(p.ID) as num";
				break;
		}
		
		$where = "";
		if( $category > self::DEFAULT_CATEGORY )
		{
			$where .= " AND p.ID IN (
				SELECT object_id FROM {$wpdb->term_relationships} AS tr INNER JOIN {$wpdb->term_taxonomy} AS tt USING(term_taxonomy_id) WHERE term_id = $category AND taxonomy = 'category'
			) ";
		}
		$where .= " AND p.post_type = '{$post_type}' AND p.post_status = '$post_status'";

		$order_by = ( $ordered ) ? "ORDER BY p.post_date DESC" : "";

		$limits = ( is_numeric( $offset ) && is_numeric( $limit ) ) ? "LIMIT $offset, $limit" : "";

		return "SELECT $fields FROM {$wpdb->posts} as p WHERE 1 = 1 $where $order_by $limits";
	}
}