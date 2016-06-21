<?php

/**
 * Description of cookieclass
 *
 * @author ovais.tariq
 */
class LM_cookies
{
	public function init()
	{
		$this->_parse_cookies_header();
	}

	private function _parse_cookies_header()
	{
		$headers = $this->_get_all_headers();

		if( false == is_array( $headers ) || count( $headers ) < 1 ) return false;

		$cookies = '';
		foreach( $headers as $key => $value )
		{
			if( strtolower( $key ) == 'cookie' )
			{
				$cookies = trim( $value );
				break;
			}
		}

		if( false == $cookies ) return false;

		$cookies = str_replace( ';', '&', $cookies );

		$_COOKIE = array();
		parse_str( $cookies, $_COOKIE );

		return true;
	}

	private function _get_all_headers()
	{
		$headers = array();

		foreach( $_SERVER as $key => $val )
		{
			if( strpos( strtolower($key), 'http_' ) === 0 )
			{
				$header_key = str_replace( ' ', '-', ucwords( str_replace( '_', ' ', strtolower( substr( $key, 5 ) ) ) ) );
				$headers[$header_key] =  $val;
			}
		}

		return $headers;
	}
}