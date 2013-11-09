<?php
/**
 * Created by Brad Walker on 5/16/13 at 4:52 AM
*/

namespace SNOAuth\Result;

require_once(dirname(__FILE__) . '/Result.php');

/**
 * Conforms to OAuth 2.0 Draft 31
 * 
 * Class OAuthAccessToken
 */
class Token extends Result {
	
	/** @var string */
	protected $accessToken;

	/** @var string|int */
	protected $clientID;

	/** @var int */
	protected $expires;

	/** @var string */
	protected $type;

	/** @var string|int */
	protected $userID;

	/** @var string */
	protected $scopes; // TODO: scopes array?


	/**
	 * @param string $accessToken
	 * @param string $clientID
	 * @param int $expires
	 * @param string $type
	 * @param string $userID
	 * @param string $scopes
	 */
	public function __construct($accessToken, $clientID, $expires, $type, $userID = NULL, $scopes = NULL) {
		$this->accessToken = $accessToken;
		$this->clientID= $clientID;
		$this->expires = $expires;
		$this->type = $type;
		$this->userID = $userID;
		$this->scopes = $scopes;
	}

	protected function generateOutputObject() {
		$output = new \stdClass;
		$output->access_token = $this->accessToken;
		$output->token_type = $this->type;
		$output->expires_in = $this->expires - time();

		return $output;
	}

	/**
	 * @return string
	 */
	public function getAccessToken()
	{
		return $this->accessToken;
	}

	/**
	 * @return int|string
	 */
	public function getClientID()
	{
		return $this->clientID;
	}

	/**
	 * @return int
	 */
	public function getExpires()
	{
		return $this->expires;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return int|string
	 */
	public function getUserID()
	{
		return $this->userID;
	}

	/**
	 * @return string
	 */
	public function getScopes()
	{
		return $this->scopes;
	}
}