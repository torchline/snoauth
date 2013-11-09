<?php
/**
 * Created by Brad Walker on 5/15/13 at 7:04 PM
*/

namespace SNOAuth;

require_once(dirname(__FILE__) . '/Config.php');

require_once(dirname(__FILE__) . '/Result/Error.php');
require_once(dirname(__FILE__) . '/Result/Token.php');

require_once(dirname(__FILE__) . '/Type/Grant.php');
require_once(dirname(__FILE__) . '/Type/Response.php');
require_once(dirname(__FILE__) . '/Type/Token.php');

if( !function_exists('apache_request_headers') ) {
	function apache_request_headers() {
		$arh = array();
		$rx_http = '/\AHTTP_/';
		foreach($_SERVER as $key => $val) {
			if( preg_match($rx_http, $key) ) {
				$arh_key = preg_replace($rx_http, '', $key);
				$rx_matches = array();
				// do some nasty string manipulations to restore the original letter case
				// this should work in most cases
				$rx_matches = explode('_', $arh_key);
				if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
					foreach($rx_matches as $ak_key => $ak_val) $rx_matches[$ak_key] = ucfirst($ak_val);
					$arh_key = implode('-', $rx_matches);
				}
				$arh[$arh_key] = $val;
			}
		}
		return( $arh );
	}
}

/**
 * Conforms to OAuth 2.0 Draft 31
 * 
 * Class SNOAuth
 */
class SNOAuth {

	/** @var Config */
	protected $config;

	/** @var array */
	protected $parameters;
	

	/**
	 * @param Config $config
	 * @param array $parameters
	 * @throws \Exception
	 */
	public function __construct($config = NULL, $parameters = NULL) {
		if (!isset($config)) {
			$config = Config::fromFile();

			if (!isset($config)) {
				throw new \Exception("No config file or object found for snoauth.", 500);
			}
		}
		$this->config = $config;

		if (!isset($parameters)) {
			$parameters = $_GET; // TODO: or whatever
		}
		$this->parameters = $parameters;
	}


	/**
	 * @param string $clientID
	 * @param string $clientSecret
	 * @param string $redirectURI
	 * 
	 * @return bool
	 */
	public function addClient($clientID, $clientSecret, $redirectURI) {
		$pdo = $this->config->getPDO();
		
		$sql = sprintf('INSERT INTO %s (%s, %s, %s) VALUES (:id, :secret, :redirectURI)',
			$this->config->clientsTable['name'],
			$this->config->clientsTable['fields']['id'],
			$this->config->clientsTable['fields']['secret'],
			$this->config->clientsTable['fields']['redirectURI']
		);
		$stmt = $pdo->prepare($sql);
		
		$success = $stmt->execute(array(
			':id' => $clientID,
			':secret' => $this->hashSecret($clientSecret, $clientID),
			':redirectURI' => $redirectURI
		));
		
		return $success;
	}

	/**
	 * @param array $array
	 * 
	 * @return int
	 */
	protected function bitwiseORArray($array) {
		$combined = array_reduce($array,
			function($a, $b) {
				return $a | $b;
			},
			0
		);
		
		return $combined;
	}
	
	/**
	 * @param string $clientSecret
	 * @param string $clientID
	 * @return string
	 */
	protected function hashSecret($clientSecret, $clientID = NULL) {
		return hash_hmac('ripemd160', "{$clientSecret}:{$clientID}", $this->config->getHashSalt());
	}
	
	protected function generateAccessToken() {
		return hash_hmac('ripemd160', uniqid('', TRUE), $this->config->getHashSalt());
	}
}


final class CredentialLocation {
	const HEADER = 4;
	const BODY = 2;
	const URL = 1;

	private static $descriptions = array(
		self::HEADER => 'header',
		self::BODY => 'body',
		self::URL => 'url'
	);

	/**
	 * @param int $locations
	 * @return array
	 */
	public static function descriptions($locations) {
		$descriptions = array();
		
		if ($locations & self::HEADER) {
			$descriptions[] = self::$descriptions[self::HEADER];
		}
		if ($locations & self::BODY) {
			$descriptions[] = self::$descriptions[self::BODY];
		}
		if ($locations & self::URL) {
			$descriptions[] = self::$descriptions[self::URL];
		}
		
		return $descriptions;
	}
}

final class ParameterLocation {
	const BODY = 2;
	const URL = 1;

	private static $descriptions = array(
		self::BODY => 'body',
		self::URL => 'url'
	);

	/**
	 * @param int $locations
	 * @return array
	 */
	public static function descriptions($locations) {
		$descriptions = array();

		if ($locations & self::BODY) {
			$descriptions[] = self::$descriptions[self::BODY];
		}
		if ($locations & self::URL) {
			$descriptions[] = self::$descriptions[self::URL];
		}

		return $descriptions;
	}
}

final class AccessTokenLocation {
	const HEADER_MAC = 8;
	const HEADER_BEARER = 4;
	const BODY = 2;
	const URL = 1; // passing access tokens in the URL is highly discouraged in OAuth 2.0 draft 31

	private static $descriptions = array(
		self::HEADER_MAC => 'header:mac',
		self::HEADER_BEARER => 'header:bearer',
		self::BODY => 'body)',
		self::URL => 'url'
	);

	/**
	 * @param int $locations
	 * @return array
	 */
	public static function descriptions($locations) {
		$descriptions = array();

		if ($locations & self::HEADER_MAC) {
			$descriptions[] = self::$descriptions[self::HEADER_MAC];
		}
		if ($locations & self::HEADER_BEARER) {
			$descriptions[] = self::$descriptions[self::HEADER_BEARER];
		}
		if ($locations & self::BODY) {
			$descriptions[] = self::$descriptions[self::BODY];
		}
		if ($locations & self::URL) {
			$descriptions[] = self::$descriptions[self::URL];
		}

		return $descriptions;
	}
}