<?php
namespace Able\Helpers;

use \Able\Helpers\Abstractions\AHelper;
use \Generator;

class Str extends AHelper {

	/**
	 * Determines whether the given value
	 * is presentable as a string.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public static final function castable(mixed $value): bool {
		return is_scalar($value)
			|| (is_object($value)

				&& (method_exists($value, '__toString') || method_exists($value, 'toString')));
	}

	/**
	 * Converts the given value into a string.
	 *
	 * @attention Throws an exception if the given value
	 * cannot be represented as a string.
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static final function cast(mixed $value): string {
		if (is_string($value)) {
			return $value;
		}

		if (is_object($value) && method_exists($value, 'toString')) {
			return $value->toString();
		}

		if (is_object($value) && method_exists($value, '__toString')) {
			return $value->__toString();
		}

		return (string)$value;
	}

	/**
	 * Converts the iterator into a string.
	 *
	 * @param Generator $Input
	 * @return string
	 */
	public static final function collect(Generator $Input): string {
		return self::join(PHP_EOL, array_map(function () {
			return self::unbreak(self::cast(func_get_arg(0)), 1);
		}, iterator_to_array($Input, false)));
	}

	/**
	 * Joins the given arguments into a single string using the given separator.
	 *
	 * @param string $separator
	 * @param mixed ...$source
	 * @return string
	 */
	public final static function join(string $separator, mixed ...$source): string {
		return implode($separator, array_map(function ($value) {
			return self::cast($value);
		}, Arr::simplify($source)));
	}

	/**
	 * @param string $source
	 * @param mixed $append
	 * @return string
	 */
	public final static function append(string $source, mixed $append): string {
		return self::join('', ...Arr::simplify(func_get_args()));
	}

	/**
	 * @param string $source
	 * @param mixed $prepend
	 * @return string
	 */
	public final static function prepend(string $source, mixed $prepend): string {
		return self::join('', ...array_reverse(Arr::simplify(func_get_args())));
	}

	/**
	 * @param string $source
	 * @param mixed $fragment
	 * @return string
	 */
	public final static function mright(string $source, mixed $fragment): string {
		return preg_replace('/(?:' . preg_quote($fragment, '/'). ')?$/', $fragment, $source, 1);
	}

	/**
	 * @param string $source
	 * @param mixed $fragment
	 * @return string
	 */
	public final static function mleft(string $source, mixed $fragment): string {
		return preg_replace('/^(?:' . preg_quote($fragment, '/'). ')?/', $fragment, $source, 1);
	}

	/**
	 * @param string $source
	 * @param int $limit
	 * @return string
	 */
	public final static function tr(string $source, int $limit): string {
		return $limit > 0 && mb_strlen($source) > $limit
			? preg_replace('/[\s,.!?:;-]+[^\s,.!?:;-]{0,' . max([10, floor($limit / 100 * 20)]) . '}$/u', '',
				mb_substr($source, 0, $limit), 1) : $source;
	}

	/**
	 * @param string $source
	 * @param int $limit
	 * @param string $finalizer
	 * @return string
	 */
	public final static function trf(string $source, int $limit, string $finalizer = '...'): string {
		return mb_strlen($source) > $limit
			? self::tr($source, $limit - mb_strlen($finalizer)) . $finalizer : $source;
	}

	/**
	 * @param string $source
	 * @param int $limit
	 * @param string|null $separator
	 * @return string
	 */
	public final static function clip(string $source, int $limit, string $separator = null): string {
		$prc = floor(mb_strlen($source) / 100 * 20);
		return $limit > 0 && mb_strlen($source) > $limit ? (self::tr($source, $limit - $prc) . $separator
			. trim(mb_substr($source, mb_strlen($source) - $prc))) : $source;
	}

	/**
	 * @param string $source
	 * @param string ...$allow
	 * @return string
	 */
	public final static function strip(string $source, string ...$allow): string {
		$allow = array_filter($allow, function($value){
			return preg_match('/^[A-Za-z0-9_-]+$/', $value); });

		return preg_replace_callback('/<((?:\/|!doctype\s*)?[a-z0-9_-]+)\s*(?:[^>\s]+(?:\s*=\s*(?:'
			. '(?:\'(?:\\\\\'|[^\'])*\'|"(?:\\\\"|[^"])*")|[^>\s]+))?\s*)*\/?>/i',

			function($value) use ($allow){
				return count($allow) < 1 || !in_array(trim($value[1], ' /'), $allow) ? '' : $value[0];
			},

		preg_replace('/<!--.*?-->/s', '', $source));
	}

	/**
	 * @param string $source
	 * @param ?int $limit
	 * @return string
	 */
	public final static function rtrim(string $source, ?int $limit = null): string {
		return preg_replace('/\s' . (!is_null($limit) && $limit >= 0
			? '{0,' . $limit . '}' : '*'). '$/s', '', $source, 1);
	}

	/**
	 * @param string $source
	 * @param int $limit
	 * @return string
	 */
	public final static function ltrim(string $source, int $limit = null): string {
		return preg_replace('/^\s' . (!is_null($limit) && $limit >= 0
			? '{0,' . $limit . '}' : '*'). '/s', '', $source, 1);
	}

	/**
	 * @param string $source
	 * @param ?int $leftLimit
	 * @param ?int $rightLimit
	 * @return string
	 */
	public final static function trim(string $source, int $leftLimit = null, int $rightLimit = null): string {
		return self::rtrim(self::ltrim($source, $leftLimit), $rightLimit);
	}

	/**
	 * @param string $source
	 * @param string $character
	 * @param int $count
	 * @return string
	 */
	public final static function rpad(string $source, string $character, int $count): string {
		return  str_pad($source, $count, $character, STR_PAD_RIGHT);
	}

	/**
	 * @param string $source
	 * @param string $character
	 * @param int $count
	 * @return string
	 */
	public final static function lpad(string $source, string $character, int $count): string {
		return  str_pad($source, $count, $character, STR_PAD_LEFT);
	}

	/**
	 * @param string $source
	 * @return string
	 */
	public final static function break(string $source): string {
		return self::unbreak($source, 1) . PHP_EOL;
	}

	/**
	 * @param string $source
	 * @param int $limit
	 * @return string
	 */
	public final static function unbreak(string $source, int $limit = -1): string{
		return preg_replace('/(?:\r\n|\n|\r)' . ($limit >= 0
			? '{0,' . $limit .'}' : '*'). '$/', '', $source, 1);
	}

	/**
	 * @param string $source
	 * @return string
	 */
	public final static function rebreak(string $source): string {
		return preg_replace('/[\n\r]+/', PHP_EOL, $source);
	}

	/**
	 * @param string $source
	 * @return string
	 */
	public final static function sanitize(string $source): string {
		return trim(preg_replace('/\s+/', ' ', $source));
	}

	/**
	 * Replaces all line breaks with a value of PHP_EOL constant.
	 *
	 * @param string $source
	 * @return string
	 */
	public final static function clnl(string $source): string {
		return preg_replace('/[\n\r]+/', PHP_EOL, $source);
	}

	/**
	 * @param string $source
	 * @return string
	 */
	public final static function rmnl(string $source): string {
		return preg_replace('/\s*[\n\r]+\s*/', ' ', $source);
	}

	/**
	 * @param string $source
	 * @param int $count
	 * @return string
	 */
	public final static function br2nl(string $source, int $count = 1): string {
		return preg_replace('/<br\s*\\/?>/', str_pad("\n", $count), $source);
	}

	/**
	 * @param string $source
	 * @return mixed
	 */
	public final static function p2nl(string $source): string {
		return preg_replace('/<\/(?:p' . (func_num_args() > 1 ? ('|' . implode('|',

		array_map(function($value){
			return preg_quote(self::cast(trim(trim($value), '<>')), '/');
		},

		array_slice(func_get_args(), 1)))) : '') . ')>/', "\$0\n", $source);
	}

	/**
	 * @param string ...$sources
	 * @return string
	 */
	public final static function md5(...$sources): string {
		return md5(self::join('', ...$sources));
	}
}
