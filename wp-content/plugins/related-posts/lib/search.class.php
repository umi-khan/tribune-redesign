<?php

class ERP_search
{
	const MINIMUM_WORD_LENGTH = 3;

	// search relevance weightages by field type
	const WEIGHTAGE_TITLE   = 8;
	const WEIGHTAGE_EXCERPT = 2;
	const WEIGHTAGE_CONTENT = 1;

	// category related weightages
	const WEIGHTAGE_CAT_MULTIPLIER_MAX = 2;
	const WEIGHTAGE_CAT_MULTIPLIER_MIN = 1;
	const WEIGHTAGE_CAT_DIVISOR_MAX    = 1.5;
	const WEIGHTAGE_CAT_DIVISOR_MIN    = 1;

	// BM25 formula normalization constants
	const BM25_K1 = 2;
	const BM25_B  = 0.5;

	private $_word_stemmer;
	private $_query_string;

	public function __construct($query_string)
	{
		$this->_word_stemmer = new ERP_word_stemmer();

		$this->_query_string = $query_string;
	}

	public function get_results($post_id = false, $interval_in_days = false, $page_num = 0, $num_posts = 10)
	{
		global $wpdb;

		// get the required values
		$search_query        = $this->_get_query_string();
		$avg_content_length  = $this->_get_content_avg_length();
		$search_query_length = strlen( str_replace( array( ',', ' ', '+', '*', '"' ), '', $search_query ) );

		// sanitize the values
		$interval_in_days = ( (int)$interval_in_days > 0 ) ? (int)$interval_in_days : false;
		$offset           = (int)$page_num * $num_posts;
		$num_posts        = ( (int)$num_posts > 0 ) ? (int)$num_posts : false;
		$post_id          = ( (int)$post_id > 0 ) ? (int)$post_id : false;
		$cats             = ( $post_id ) ? $this->_get_post_categories( $post_id ) : false;
		$cats             = ( is_array( $cats ) && count( $cats ) > 0 ) ? implode( ', ', $cats ) : false;
		$search_query     = $wpdb->escape( $search_query );

		// content weightages related
		$title_weight   = self::WEIGHTAGE_TITLE;
		$excerpt_weight = self::WEIGHTAGE_EXCERPT;
		$content_weight = self::WEIGHTAGE_CONTENT;

		// category weightages related
		$cat_multiplier_weight_max = self::WEIGHTAGE_CAT_MULTIPLIER_MAX;
		$cat_multiplier_weight_min = self::WEIGHTAGE_CAT_MULTIPLIER_MIN;
		$cat_divisor_weight_max    = self::WEIGHTAGE_CAT_DIVISOR_MAX;
		$cat_divisor_weight_min    = self::WEIGHTAGE_CAT_DIVISOR_MIN;

		// BM25 constants
		$k1 = self::BM25_K1;
		$b  = self::BM25_B;

		// BM25 keyword relativity formula
		$bm25_formula = "
			(
				(
					(
						$title_weight     * MATCH(post_title)   AGAINST ('$search_query' IN BOOLEAN MODE)
						+ $excerpt_weight * MATCH(post_excerpt) AGAINST ('$search_query' IN BOOLEAN MODE)
						+ $content_weight * MATCH(post_content) AGAINST ('$search_query' IN BOOLEAN MODE)
					)
					* $search_query_length " .
					( ( $cats ) ? " * IF(term_id IN ($cats) OR parent IN ($cats), $cat_multiplier_weight_max, $cat_multiplier_weight_min) " : " " ) . "
				)
				/
				(
					$k1 * (1 - $b) + ( $b * LENGTH(post_content) / $avg_content_length )
					+ (
							$title_weight     * MATCH(post_title)   AGAINST ('$search_query' IN BOOLEAN MODE)
							+ $excerpt_weight * MATCH(post_excerpt) AGAINST ('$search_query' IN BOOLEAN MODE)
							+ $content_weight * MATCH(post_content) AGAINST ('$search_query' IN BOOLEAN MODE)
						)
					* $search_query_length " .
					( ( $cats ) ? " * IF(term_id IN ($cats) OR parent IN ($cats), $cat_divisor_weight_max, $cat_divisor_weight_min) " : " " ) . "
				)
			)";

		// create the select clause
		$select = "
				ID, post_title, post_date, 
				MATCH(post_title)   AGAINST ('$search_query' IN BOOLEAN MODE) AS score_title,
				MATCH(post_excerpt) AGAINST ('$search_query' IN BOOLEAN MODE) AS score_excerpt,
				MATCH(post_content) AGAINST ('$search_query' IN BOOLEAN MODE) AS score_content,
				" . ( ( $cats ) ? "IF(term_id IN ($cats) OR parent IN ($cats), $cat_multiplier_weight_max, $cat_multiplier_weight_min) AS cat_relativity," : "" ) . "
				$bm25_formula AS score";

		// create the from clause
		$from = " (
				SELECT ID, post_title, post_excerpt, post_content, post_date
				FROM {$wpdb->posts}
				WHERE post_type = 'post'
				AND post_status = 'publish'" . "
				" . ( ( $interval_in_days ) ? " AND post_date >= CURRENT_TIMESTAMP - INTERVAL $interval_in_days DAY" : "" ) . "
				" . ( ( $post_id ) ? " AND ID <> $post_id" : "" )  . "
			) AS p ";
			
		if( $cats )
			$from .= "
				LEFT JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id
				LEFT JOIN {$wpdb->term_taxonomy}      AS tt USING(term_taxonomy_id) ";

		// create the where clause
		$where = "MATCH(post_title, post_excerpt, post_content) AGAINST ('$search_query' IN BOOLEAN MODE)";

		// create the limit clause
		$limit = ( $num_posts ) ? "LIMIT $offset, $num_posts" : "";

		// create the required sql which uses BM25 implementation
		$sql = "
			SELECT $select
			FROM   $from
			WHERE  $where
			GROUP  BY ID
			ORDER  BY score DESC, post_date DESC
			$limit";

		return $wpdb->get_results( $sql );
	}

	private function _get_content_avg_length($interval_in_days = false)
	{
		global $wpdb;

		$interval_in_days = ( (int)$interval_in_days > 0 ) ? (int)$interval_in_days : false;

		$sql = "
			SELECT ROUND(AVG(LENGTH(post_content)), 3) as avg_length
			FROM {$wpdb->posts}
			WHERE post_type = 'post' AND post_status = 'publish'";

		if( $interval_in_days ) $sql .= " AND post_date >= CURRENT_TIMESTAMP - INTERVAL $interval_in_days DAY";

		return $wpdb->get_var( $sql );
	}

	private function _get_post_categories($post_id = false)
	{
		if( (int)$post_id < 1 ) return array();

		$post_categories = get_the_category( $post_id );
		$categories      = array();
		foreach( (array)$post_categories as $cat ) $categories[] = $cat->cat_ID;

		return $categories;
	}

	/**
	 * @return string A query string that can be used in boolean full text searches
	 */
	private function _get_query_string()
	{
		return implode( " ", $this->_get_query_array() );
	}

	private function _get_query_array()
	{
		$this->_query_string = strtolower( trim( stripslashes( $this->_query_string ) ) );

		$words = array();
		preg_match_all( '/"[^"]+"|[^"\s,]+/', $this->_query_string, $words );

		if( empty( $words ) ) return false;

		return $this->_build_query( $words );
	}

	private function _build_query($words_list, $keywords_list = array())
	{
		if( false == is_array( $words_list ) || count( $words_list ) < 1 ) return $keywords_list;

		$keyword_count        = 0;
		$prepend_and_operator = false;

		foreach( (array)$words_list as $word )
		{
			// if the word is a list of words and not a single word iterate over the entire list
			if( is_array( $word ) && count( $word ) > 0 ) return $this->_build_query( $word, $keywords_list );

			// if the word is an empty list skip it
			if( is_array( $word ) && count( $word ) < 1 ) continue;

			// if the length of the word does not match the minimum threshold skip it
			if( strlen( $word ) < self::MINIMUM_WORD_LENGTH ) continue;

			// if the word is already present in the list skip it
			$stemmed_word = $this->_word_stemmer->Stem( $word );

			if( in_array( $word, $keywords_list )            || in_array( "+$word", $keywords_list ) ||
				 in_array( "$word*", $keywords_list )         || in_array( "+$word*", $keywords_list ) ||
				 in_array( $stemmed_word, $keywords_list )    || in_array( "+$stemmed_word", $keywords_list ) ||
				 in_array( "$stemmed_word*", $keywords_list ) || in_array( "+$stemmed_word*", $keywords_list ) )
			{
				continue;
			}

			// if the word is an 'or' skip it
			if( $word == 'or' ) continue;

			// if the word is an 'and' prepend a '+' to neighbouring words
			if( $word == 'and' )
			{
				//prepend the operator '+' to the previous word if the word already doesnt contain '+' in the beginning
				if( ( $keyword_count > 0 ) &&
					 ( false == strstr( $keywords_list[$keyword_count - 1], '+' ) || 
								( strpos( $keywords_list[$keyword_count - 1], '+' ) > 0 ) ) )
				{
					$keywords_list[$keyword_count - 1] = '+' . $keywords_list[$keyword_count - 1];
				}

				// prepend the operator '+' to the next word if the word already doesnt contain '+' in the beginning
				$prepend_and_operator = true;

				continue;
			}

			if( $prepend_and_operator )
			{
				if( false == strstr( $word, '+' ) || ( strpos( $word, '+' ) > 0 ) ) $word = '+' . $word;

				$prepend_and_operator = false;
			}
			
			// if the word is a phrase or if it cant be stemmed leave it as it is, otherwise stem the word
			if( false == $this->_is_phrase( $word ) && ( $word != $stemmed_word ) ) $word = $stemmed_word . '*';

			// add the word to our keywords list
			$keywords_list[] = $word;
			$keyword_count++;
		}

		return $keywords_list;
	}

	private function _is_phrase($word)
	{
		$charactersCount = count_chars( $word, 1 );

		foreach( (array)$charactersCount as $character => $count )
		{
			if( chr( $character ) == '"' && $count == 2 ) return true;
		}

		return false;
	}
}