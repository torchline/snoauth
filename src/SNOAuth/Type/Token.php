<?php
/**
 * Created by Brad Walker on 5/15/13 at 7:15 PM
 */

namespace SNOAuth\Type;

/**
 * Conforms to OAuth 2.0 Draft 31
 *
 * Class Response
 */
final class Token {
	const BEARER = 'bearer';
	const MAC = 'mac';

	private function __construct() {}
}