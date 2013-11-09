<?php
/**
 * Created by Brad Walker on 5/15/13 at 7:01 PM
*/

namespace SNOAuth\Type;

/**
 * Conforms to OAuth 2.0 Draft 31
 * 
 * Class Grant
 */
final class Grant {
	const AUTHORIZATION_CODE = 'authorization_code';
	const RESOURCE_OWNER_PASSWORD_CREDENTIALS = 'password';
	const CLIENT_CREDENTIALS = 'client_credentials';
	const REFRESH_TOKEN = 'refresh_token';

	private function __construct() {}
	
	public static function from($grantType) {
		if (strcasecmp($grantType, self::AUTHORIZATION_CODE) === 0) {
			return self::AUTHORIZATION_CODE;
		}
		else if (strcasecmp($grantType, self::RESOURCE_OWNER_PASSWORD_CREDENTIALS) === 0) {
			return self::RESOURCE_OWNER_PASSWORD_CREDENTIALS;
		}
		else if (strcasecmp($grantType, self::CLIENT_CREDENTIALS) === 0) {
			return self::CLIENT_CREDENTIALS;
		}
		else if (strcasecmp($grantType, self::REFRESH_TOKEN) === 0) {
			return self::REFRESH_TOKEN;
		}
		else {
			return NULL;
		}
	}
}