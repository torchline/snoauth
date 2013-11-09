<?php
/**
 * Created by Brad Walker on 10/20/13 at 9:38 PM
*/

namespace SNOAuth\Result;

require_once(dirname(__FILE__) . '/Result.php');

class Error extends Result {
	/**
	 * The request is missing a required parameter, includes an
	 * unsupported parameter value (other than grant type),
	 * repeats a parameter, includes multiple credentials,
	 * utilizes more than one mechanism for authenticating the
	 * client, or is otherwise malformed.
	 */
	const INVALID_REQUEST = 'invalid_request';

	/**
	 * Client authentication failed (e.g. unknown client, no
	 * client authentication included, or unsupported
	 * authentication method).
	 */
	const INVALID_CLIENT = 'invalid_client';

	/**
	 * The provided authorization grant (e.g. authorization
	 * code, resource owner credentials) or refresh token is
	 * invalid, expired, revoked, does not match the redirection
	 * URI used in the authorization request, or was issued to
	 * another client.
	 */
	const INVALID_GRANT = 'invalid_grant';

	/**
	 * The authenticated client is not authorized to use this
	 * authorization grant type.
	 */
	const UNAUTHORIZED_CLIENT = 'unauthorized_client';

	/**
	 * The authorization grant type is not supported by the
	 * authorization server.
	 */
	const UNSUPPORTED_GRANT_TYPE = 'unsupported_grant_type';

	/**
	 * The requested scope is invalid, unknown, malformed, or
	 * exceeds the scope granted by the resource owner.
	 */
	const INVALID_SCOPE = 'invalid_scope';


	protected static $descriptions = array(
		self::INVALID_REQUEST => 'The request is missing a required parameter, includes an unsupported parameter value (other than grant type), repeats a parameter, includes multiple credentials, utilizes more than one mechanism for authenticating the client, or is otherwise malformed.',
		self::INVALID_CLIENT => 'Client authentication failed (e.g. unknown client, no client authentication included, or unsupported authentication method)',
		self::INVALID_GRANT => 'The provided authorization grant (e.g. authorization code, resource owner credentials) or refresh token is invalid, expired, revoked, does not match the redirection URI used in the authorization request, or was issued to another client.',
		self::UNAUTHORIZED_CLIENT => 'The authenticated client is not authorized to use this authorization grant type.',
		self::UNSUPPORTED_GRANT_TYPE => 'The authorization grant type is not supported by the authorization server.',
		self::INVALID_SCOPE => 'The requested scope is invalid, unknown, malformed, or exceeds the scope granted by the resource owner.'
	);


	/** @var string */
	protected $errorType;
	protected $description;
	protected $uri;


	public function __construct($errorType) {
		switch ($errorType) {
			case self::INVALID_REQUEST:
			case self::INVALID_CLIENT:
			case self::INVALID_GRANT:
			case self::UNAUTHORIZED_CLIENT:
			case self::UNSUPPORTED_GRANT_TYPE:
			case self::INVALID_SCOPE:
				$this->errorType = $errorType;
				$this->description = self::$descriptions[$errorType];
				$this->uri = 'http://tools.ietf.org/html/draft-ietf-oauth-v2-31#section-5.2';
				break;
			default:
				die('ERROR: Invalid error supplied to OAuthError constructor');
				break;
		}
	}

	protected function generateOutputObject() {		
		$output = new \stdClass;
		$output->error = $this->errorType;

		if (isset($this->description)) {
			$output->error_description = $this->description;
		}
		
		if (isset($this->uri)) {
			$output->error_uri = $this->uri;
		}


		return $output;
	}





	/**
	 * @return string
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getURI() {
		return $this->uri;
	}
} 