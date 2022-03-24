<?php
/**
 * Copyright © 2012 - 2021 by Renaud Guillard (dev@nore.fr)
 * Distributed under the terms of the MIT License, see LICENSE
 */

/**
 *
 * @package Http
 */
namespace NoreSources\Http;

/**
 * Hypertext Transfer Protocol (HTTP/1.1): Authentication
 *
 * @see https://tools.ietf.org/html/rfc7235
 */
class RFC7235
{

	/**
	 * The token68 syntax allows the 66 unreserved URI characters
	 * ([RFC3986]), plus a few others, so that it can hold a base64,
	 * base64url (URL and filename safe alphabet), base32, or base16 (hex)
	 * encoding, with or without padding, but excluding whitespace
	 * ([RFC4648]).
	 *
	 * @var string
	 */
	const TOKEN68_PATTERN = '[a-zA-Z0-9._~+/-]+=*';

	const AUTH_SCHEME_PATTERN = RFC7230::TOKEN_PATTERN;

	const AUTH_PARAM_VALUE_PATTERN = '(' . RFC7230::TOKEN_PATTERN . ')|' .
		RFC7230::QUOTED_STRING_PATTERN;

	/**
	 *
	 * auth-param = token BWS "=" BWS ( token / quoted-string )
	 *
	 * @var string
	 */
	const AUTH_PARAM_PATTERN = '(' . RFC7230::TOKEN_PATTERN . ')' .
		RFC7230::BWS_PATTERN . '=' . RFC7230::BWS_PATTERN .
		self::AUTH_PARAM_VALUE_PATTERN;

	const AUTH_PARAM_LIST_PATTERN = self::AUTH_PARAM_PATTERN . '(?:' .
		RFC7230::BWS_PATTERN . ',' . RFC7230::BWS_PATTERN .
		self::AUTH_PARAM_PATTERN . ')*';
}



