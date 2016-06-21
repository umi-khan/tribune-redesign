<?php

/**
 * Description of excerpt_adminclass
 *
 * @author ovais.tariq
 */
class SM_content_admin
{
	// Excerpt Max Length in Characters
	const STORY_EXCERPT_LENGTH	=	118;
	
	public static function activate()
	{
		
	}

	public function hook_actions()
	{
		if( is_admin() )
		{
			// insert all required javascript
			add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );

			// insert all the required css
			add_action( 'admin_print_styles', array( $this, 'init_stylesheets' ) );
		}
	}

	public function hook_filters()
	{
		if( is_admin() )
		{
			// content related
			add_filter( 'content_save_pre', array( $this, 'content_save_pre' ) );

			// excerpt related
			add_filter( 'excerpt_save_pre', array( $this, 'excerpt_save_pre' ) );
		}
	}

	public function admin_print_scripts()
	{
		wp_enqueue_script( 'jquery-charslimit', SM_PLUGIN_URL . 'js/jquery.chars_limit.js', array( 'jquery' ) );
		wp_enqueue_script( 'jquery-spotlight', SM_PLUGIN_URL . 'js/jquery.spotlight.js', array( 'jquery' ) );

		wp_enqueue_script( 'sm-excerpt-admin', SM_PLUGIN_URL . 'js/excerpt.js',
				  array( 'jquery', 'jquery-charslimit', 'jquery-spotlight' ), '1.1' );

		wp_localize_script( 'sm-excerpt-admin', 'SM_story_config', array( 'excerpt_max_length'  => self::STORY_EXCERPT_LENGTH ) );
	}

	public function init_stylesheets()
	{
		wp_enqueue_style( 'sm-excerpt-admin', SM_PLUGIN_URL . 'css/excerpt_admin.css' );
	}

	public function excerpt_save_pre($excerpt)
	{
		global $post;

		if( $post->post_type !== 'post' ) return $excerpt;

		if( $post->post_status !== 'publish' ) return $excerpt;

		return self::trim_content( $excerpt, self::STORY_EXCERPT_LENGTH );
	}

	/**
	* Removes all FONT and SPAN tags, and all Class and Style attributes.
	* Designed to get rid of non-standard Microsoft Word HTML tags.
	*/
	public function content_save_pre($content)
	{
		$content = ereg_replace( "<(/)?(font|span|del|ins|script)[^>]*>", "", $content );

		// then run another pass over the html (twice), removing unwanted attributes
		$content = ereg_replace( "<([^>]*)(lang|style|size|face|dir)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>","<\\1>", $content );
		$content = ereg_replace( "<([^>]*)(lang|style|size|face|dir)=(\"[^\"]*\"|'[^']*'|[^>]+)([^>]*)>","<\\1>", $content );
		
		return $content;
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
}