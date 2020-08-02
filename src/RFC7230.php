<?php
/**
 * Copyright © 2012 - 2020 by Renaud Guillard (dev@nore.fr)
 * Distributed under the terms of the MIT License, see LICENSE
 */

/**
 *
 * @package Http
 */
namespace NoreSources\Http;

/**
 * Hypertext Transfer Protocol (HTTP/1.1): Message Syntax and Routing
 *
 * @see https://tools.ietf.org/html/rfc7230
 */
class RFC7230
{

	/**
	 * From RFC 7230 Section 3.2.6
	 *
	 * Delimiters are chosen
	 * from the set of US-ASCII visual characters not allowed in a token
	 * (DQUOTE and "(),/:;<=>?@[\]{}").
	 *
	 * @var string
	 *
	 * @see https://tools.ietf.org/html/rfc7230#section-3.2.6
	 */
	const DELIMITER_PATTERN = '["(),/:;<=>?@[\]{}]';

	/**
	 * Optional whitespace
	 *
	 * @var string
	 */
	const OWS_PATTERN = '[\t ]*';

	/**
	 * Bad whitespace
	 *
	 * @var string
	 */
	const BWS_PATTERN = self::OWS_PATTERN;

	/**
	 * Visible characters
	 *
	 * @var string
	 */
	const VCHAR_RANGE = '\x21-\x7E';

	/**
	 * Set of characters authorized in tokens
	 *
	 * @var string
	 */
	const TOKEN_CHAR_RANGE = 'A-Za-z0-9!#$%\'*+.^_`|~\x2D';

	/**
	 * From RFC 7230 Section 3.2.6
	 *
	 * token = 1*tchar
	 *
	 * tchar = "!" / "#" / "$" / "%" / "&" / "'" / "*"
	 * / "+" / "-" / "." / "^" / "_" / "`" / "|" / "~"
	 * / DIGIT / ALPHA
	 * ; any VCHAR, except delimiters
	 *
	 * @var string #see https://tools.ietf.org/html/rfc7230#section-3.2.6
	 */
	const TOKEN_PATTERN = '[' . self::TOKEN_CHAR_RANGE . ']+';

	/**
	 * obs-text = %x80-FF
	 *
	 * @var string
	 */
	const OBS_TEXT_RANGE = '\x80-\xFF';

	/**
	 * Quoted character
	 *
	 * quoted-pair = "\" ( HTAB / SP / VCHAR / obs-text )
	 *
	 * @var string
	 */
	const QUOTED_PAIR_PATTERN = '\x5C[\x09\x20' . self::VCHAR_RANGE . self::OBS_TEXT_RANGE . ']';

	/**
	 *
	 * qdtext = HTAB / SP /%x21 / %x23-5B / %x5D-7E / obs-text
	 *
	 * @var string
	 */
	const QUOTED_TEXT_RANGE = '\x09\x20\x21\x23-\x5B\x5D-\x7E' . self::OBS_TEXT_RANGE;

	/**
	 * Double quote
	 *
	 * @var string
	 */
	const DQUOTE = '\x22';

	/**
	 * From RFC 7230 Section 3.2.6
	 *
	 * Capture group represents the string inside double quotes
	 *
	 * quoted-string = DQUOTE *( qdtext / quoted-pair ) DQUOTE
	 *
	 * @var string
	 *
	 * @see https://tools.ietf.org/html/rfc7230#section-3.2.6
	 */
	const QUOTED_STRING_PATTERN = self::DQUOTE . '((?:(?:' . self::QUOTED_PAIR_PATTERN . ')|(?:[' .
		self::QUOTED_TEXT_RANGE . ']+))*)' . self::DQUOTE;

	/**
	 * Parameter value
	 *
	 * Value is located in capture group #1 if the value is a token, in group #2 if i'ts a quoted
	 * value
	 *
	 * parameter-value = ( token / quoted-string )
	 *
	 * @var string
	 */
	const PARAMETER_VALUE_PATTERN = '(?:(' . self::TOKEN_PATTERN . ')|' . self::QUOTED_STRING_PATTERN .
		')';

	/**
	 * Parameter
	 *
	 * parameter = parameter-name "=" parameter-value
	 *
	 * @var unknown
	 */
	const PARAMETER_PATTERN = '(' . self::TOKEN_PATTERN . ')=' . self::PARAMETER_VALUE_PATTERN;

	/**
	 * Http header field name pattern
	 *
	 * @var string
	 * @see https://tools.ietf.org/html/rfc7230
	 */
	const HEADER_FIELD_NAME_PATTERN = '^([a-z0-9!#$%\'*+.^_`|~-]+)$';

	const HEADER_FIELD_NAME_PATTERN_OPTION = 'i';
}