<?php
/**
 * Created by Brad Walker on 5/15/13 at 8:33 PM
*/

namespace SNOAuth;

require_once('SNOAuth2DelegateInterface.php');
require_once('SNOAuth.php');

/** 
 * @property \PDO $pdo
 * @property \SNOAuth2DelegateInterface $delegate
 * 
 * Class OAuth2Config
 */
class Config {
	
	protected $configArray;
	
	protected $pdo;
	protected $delegate;

	protected $showErrorDescriptions = TRUE;
	protected $showErrorURIs = TRUE;
	protected $accessTokenLifetime = 3600;
	protected $hashSalt = 'axv87twregl2rblkjp';


	protected $grantParameterLocations = array(
		ParameterLocation::BODY,
		ParameterLocation::URL
	);

	protected $grantClientCredentialLocations = array(
		CredentialLocation::HEADER,
		CredentialLocation::BODY,
		CredentialLocation::URL
	);

	protected $resourceAccessTokenLocations = array(
		AccessTokenLocation::HEADER_BEARER,
		AccessTokenLocation::BODY,
		AccessTokenLocation::URL
	);


	protected $clientsTable = array(
		'name' => 'OAuthClient',
		'fields' => array(
			'id' => 'ID',
			'secret' => 'Secret',
			'redirectURI' => 'RedirectURI'
		)
	);

	protected $accessTokensTable = array(
		'name' => 'OAuthAccessToken',
		'fields' => array(
			'clientID' => 'ClientID',
			'accessToken' => 'AccessToken',
			'expires' => 'Expires',
			'userID' => 'UserID',
			'scope' => 'Scope'
		)
	);
	
	
	/**
	 * @param \PDO $pdo
	 */
	public function __construct($pdo = NULL) {
		$this->pdo = $pdo;
	}

	
	/**
	 * @param string $path
	 * @return Config
	 * @throws \Exception
	 */
	public static function fromFile($path = 'snoauth-config.php') {
		if (!file_exists($path)) {
			throw new \Exception("No snoauth config file at '{$path}'", 500);
		}

		/** @noinspection PhpIncludeInspection */
		include($path);

		if (!isset($config)) {
			throw new \Exception("No config variable found in snoauth config file at '{$path}'", 500);
		}

		// PDO
		if (!isset($config['db'])) {
			throw new \Exception("No db config Setting specified in snoauth config file at '{$path}'", 500);
		}

		// create Config
		$snoauthConfig = new Config();
		$snoauthConfig->setConfigArray($config);

		return $snoauthConfig;
	}


	/**
	 * @param array $configArray
	 */
	public function setConfigArray($configArray) {
		$this->configArray = $configArray;
	}

	/**
	 * @param int $accessTokenLifetime
	 */
	public function setAccessTokenLifetime($accessTokenLifetime)
	{
		$this->accessTokenLifetime = $accessTokenLifetime;
	}

	/**
	 * @return int
	 */
	public function getAccessTokenLifetime()
	{
		return $this->accessTokenLifetime;
	}

	/**
	 * @param array $accessTokensTable
	 */
	public function setAccessTokensTable($accessTokensTable)
	{
		$this->accessTokensTable = $accessTokensTable;
	}

	/**
	 * @return array
	 */
	public function getAccessTokensTable()
	{
		return $this->accessTokensTable;
	}

	/**
	 * @param array $clientsTable
	 */
	public function setClientsTable($clientsTable)
	{
		$this->clientsTable = $clientsTable;
	}

	/**
	 * @return array
	 */
	public function getClientsTable()
	{
		return $this->clientsTable;
	}

	/**
	 * @param array $grantClientCredentialLocations
	 */
	public function setGrantClientCredentialLocations($grantClientCredentialLocations)
	{
		$this->grantClientCredentialLocations = $grantClientCredentialLocations;
	}

	/**
	 * @return array
	 */
	public function getGrantClientCredentialLocations()
	{
		return $this->grantClientCredentialLocations;
	}

	/**
	 * @param array $grantParameterLocations
	 */
	public function setGrantParameterLocations($grantParameterLocations)
	{
		$this->grantParameterLocations = $grantParameterLocations;
	}

	/**
	 * @return array
	 */
	public function getGrantParameterLocations()
	{
		return $this->grantParameterLocations;
	}

	/**
	 * @param string $hashSalt
	 */
	public function setHashSalt($hashSalt)
	{
		$this->hashSalt = $hashSalt;
	}

	/**
	 * @return string
	 */
	public function getHashSalt()
	{
		return $this->hashSalt;
	}

	/**
	 * @param mixed $pdo
	 */
	public function setPDO($pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * @return \PDO
	 */
	public function getPDO()
	{
		return $this->pdo;
	}

	/**
	 * @param array $resourceAccessTokenLocations
	 */
	public function setResourceAccessTokenLocations($resourceAccessTokenLocations)
	{
		$this->resourceAccessTokenLocations = $resourceAccessTokenLocations;
	}

	/**
	 * @return array
	 */
	public function getResourceAccessTokenLocations()
	{
		return $this->resourceAccessTokenLocations;
	}

	/**
	 * @param boolean $showErrorDescriptions
	 */
	public function setShowErrorDescriptions($showErrorDescriptions)
	{
		$this->showErrorDescriptions = $showErrorDescriptions;
	}

	/**
	 * @return boolean
	 */
	public function getShowErrorDescriptions()
	{
		return $this->showErrorDescriptions;
	}

	/**
	 * @param boolean $showErrorURIs
	 */
	public function setShowErrorURIs($showErrorURIs)
	{
		$this->showErrorURIs = $showErrorURIs;
	}

	/**
	 * @return boolean
	 */
	public function getShowErrorURIs()
	{
		return $this->showErrorURIs;
	}
	
	
}