<?php
/**
 * Created by Brad Walker on 10/20/13 at 9:39 PM
*/

namespace SNOAuth\Result;


abstract class Result {
	
	protected $httpStatusCode = 200;
	
	abstract protected function generateOutputObject();
	
	public function output() {
		header( "{$_SERVER['SERVER_PROTOCOL']} {$this->httpStatusCode}", TRUE, $this->$httpStatusCode);
		header('Content-Type: application/json');
		header('Cache-Control: no-store');
		header('Pragma: no-cache');

		$outputObject = $this->generateOutputObject();
		die(json_encode($outputObject));
	}
} 