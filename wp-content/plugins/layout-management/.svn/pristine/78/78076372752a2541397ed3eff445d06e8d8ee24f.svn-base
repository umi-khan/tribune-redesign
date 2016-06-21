<?php

/**
 * Description of HTML_JSON_response_helper
 *
 * @author ovais.tariq
 */
class HTML_JSON_response_helper
{
	public static function send_html($content)
	{
		// clean the output buffer
		while(@ob_end_clean());

		header("Content-Type: text/html");

		echo $content;

		exit;
	}

	public static function send_json($result, $error = NULL, $id = 0)
	{
		$response = new _json_response();
		$response->result = $result;
		$response->error = $error;
		$response->id = $id;

		// clean the output buffer
		while(@ob_end_clean());

		header("Content-Type: application/json");

		echo json_encode($response);

		exit;
	}
}

class _json_response
{
    /**
     * The Object that was returned by the invoked method. This must be null in case there was an error invoking the method.
     * @var object
     * @access Public
     */
    public $result;

    /**
     * An Error object if there was an error invoking the method. It must be null if there was no error. 
     * @var object
     * @access Public
     */
    public $error;

    /**
     * This must be the same id as the request it is responding to. 
     * @var int|string
     * @access Public
     */
    public $id;
}