<?php
/**
 * Created by Brad Walker on 10/23/13 at 8:29 PM
*/

namespace SNOAuth\Request;


class AccessToken extends Request {
	
	/** @var int */
	protected $grantType;
	
	/** @var string */
	protected $clientID;
	
	/** @var string */
	protected $clientSecret;
	
	/** @var string */
	protected $scope;

	
	/**
	 * @param int $grantType
	 * @param string $clientID
	 * @param string $clientSecret
	 * @param string $scope
	 */
	function __construct($grantType, $clientID, $clientSecret, $scope) {
		$this->grantType = $grantType;
		$this->clientID = $clientID;
		$this->clientSecret = $clientSecret;
		$this->scope = $scope;
	}


	/**
	 * @return string
	 */
	public function getClientID() {
		return $this->clientID;
	}

	/**
	 * @return string
	 */
	public function getClientSecret() {
		return $this->clientSecret;
	}

	/**
	 * @return int
	 */
	public function getGrantType() {
		return $this->grantType;
	}

	/**
	 * @return string
	 */
	public function getScope() {
		return $this->scope;
	}
} 