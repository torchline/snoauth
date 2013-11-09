<?php
/**
 * Created by Brad Walker on 10/23/13 at 10:22 PM
*/

namespace SNOAuth;


class ClientCredentials extends SNOAuth {

	/**
	 * @param Config $config
	 * @param array $parameters
	 * @return ClientCredentials
	 */
	public static function create($config = NULL, $parameters = NULL) {
		return new ClientCredentials($config, $parameters);
	}
	
	/**
	 * @param bool $inline
	 * @return Result\Result
	 */
	public function requestAccessToken($inline = FALSE) {
		$result = $this->generateAccessTokenResult();
		
		if ($inline) {
			$result->output();
		}

		return $result;
	}

	/**
	 * @return Result\Result
	 */
	protected function generateAccessTokenResult() {
		// Grant Type
		if (!isset($this->parameters['grant_type'])) {
			return new Result\Error(Result\Error::INVALID_REQUEST);
		}

		$grantType = Type\Grant::from($this->parameters['grant_type']);
		if (!isset($grantType)) {
			return new Result\Error(Result\Error::UNSUPPORTED_GRANT_TYPE);
		}

		// Client ID
		if (!isset($this->parameters['client_id'])) {
			return new Result\Error(Result\Error::INVALID_REQUEST);
		}
		$clientID = $this->parameters['client_id'];

		// Client Secret
		if (!isset($this->parameters['client_secret'])) {
			return new Result\Error(Result\Error::INVALID_REQUEST);
		}
		$clientSecret = $this->parameters['client_secret'];

		// Scope
		$scope = isset($this->parameters['scope']) ? $this->parameters['scope'] : NULL;
		// TODO: check if valid scope
		
		$hasValidCredentials = $this->verifyClientCredentials($clientID, $clientSecret, $error);
		if (isset($error)) {
			return $error;
		}
		
		if (!$hasValidCredentials) {
			return new Result\Error(Result\Error::INVALID_CLIENT);
		}

		$token = $this->fetchToken($clientID, $scope, $error);
		if (isset($error)) {
			return $error;
		}
		
		return $token;
	}

	/**
	 * @param string $clientID
	 * @param string $clientSecret
	 * @param Result\Error $error
	 * @return bool
	 */
	protected function verifyClientCredentials($clientID, $clientSecret, &$error) {
		// check db for client id and secret match
		$clientsTable = $this->config->getClientsTable();
		
		$sql = sprintf('SELECT COUNT(1) AS Count FROM %s WHERE %s = :id && %s = :secret',
			$clientsTable['name'],
			$clientsTable['fields']['id'],
			$clientsTable['fields']['secret']
		);
		
		$pdo = $this->config->getPDO();
		$stmt = $pdo->prepare($sql);

		$success = $stmt->execute(array(
			':id' => $clientID,
			':secret' => $this->hashSecret($clientSecret, $clientID)
		));

		if (!$success) {
			// TODO: error for db failure
			$error = NULL;
			die('verify client credentials db fail');
		}

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$result = $results[0];
		$count = $result['Count'];
		$isValid = $count > 0;
		
		return $isValid;
	}

	/**
	 * @param string $clientID
	 * @param string $scope
	 * @param Result\Error $error
	 * @return Result\Token
	 */
	protected function fetchToken($clientID, $scope = NULL, &$error) {
		$accessTokensTable = $this->config->getAccessTokensTable();
		
		$sql = sprintf('SELECT %s, %s, %s FROM %s WHERE %s = :clientID AND %s = :scope ORDER BY %s DESC LIMIT 1',
			$accessTokensTable['fields']['accessToken'],
			$accessTokensTable['fields']['expires'],
			$accessTokensTable['fields']['scope'],
			$accessTokensTable['name'],
			$accessTokensTable['fields']['clientID'],
			$accessTokensTable['fields']['scope'],
			$accessTokensTable['fields']['expires']
		);
		
		$pdo = $this->config->getPDO();
		$stmt = $pdo->prepare($sql);

		$success = $stmt->execute(array(
			':clientID' => $clientID,
			':scope' => $scope
		));

		// TODO: error on db fail
		if (!$success) {
			$error = NULL;
			die('not success: '.implode(' ', $stmt->errorInfo()));
		}

		$results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		
		if (count($results) == 0) {
			// generate new token
			$newToken = new Result\Token(
				$this->generateAccessToken(),
				$clientID,
				time() + $this->config->getAccessTokenLifetime(),
				Type\Token::BEARER,
				NULL,
				$scope
			);
			
			$this->saveToken($newToken, $error);
			if (isset($error)) {
				return NULL;
			}
			
			return $newToken;
		}
		
		$result = $results[0];
		
		// pick first one and delete others
		$accessTokenFieldName = $accessTokensTable['fields']['accessToken'];
		$expiresFieldName = $accessTokensTable['fields']['expires'];

		$accessToken = $result[$accessTokenFieldName];
		$expires = $result[$expiresFieldName];

		if ($expires < time()) {
			return NULL;
		}

		return new Result\Token(
			$accessToken,
			$clientID,
			$expires,
			Type\Token::BEARER,
			NULL,
			$scope
		);
	}

	/**
	 * @param Result\Token $token
	 * @param Result\Error $error
	 * @return bool
	 */
	protected function saveToken($token, &$error) {
		$accessTokensTable = $this->config->getAccessTokensTable();
		
		$sql = sprintf('INSERT INTO %s (%s, %s, %s, %s, %s) VALUES (:clientID, :accessToken, :expires, :userID, :scope)',
			$accessTokensTable['name'],
			$accessTokensTable['fields']['clientID'],
			$accessTokensTable['fields']['accessToken'],
			$accessTokensTable['fields']['expires'],
			$accessTokensTable['fields']['userID'],
			$accessTokensTable['fields']['scope']
		);
		
		$pdo = $this->config->getPDO();
		$stmt = $pdo->prepare($sql);

		$success = $stmt->execute(array(
			':clientID' => $token->getClientID(),
			':accessToken' => $token->getAccessToken(),
			':expires' => $token->getExpires(),
			':userID' => $token->getUserID(),
			':scope' => $token->getScopes(), // TODO: array idk
		));

		if (!$success) {
			$error = NULL;
			// TODO: error on fail db
			die('failed saving access token object: '.implode(' ', $stmt->errorInfo()));
		}
	}
} 