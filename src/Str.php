<?php
namespace Able\Helpers;

use \Able\Reglib\Reglib;

use \Able\Helpers\Abstractions\AHelper;

class Str extends AHelper {

	/**
	 * Converts any value into a string.
	 *
	 * @attention This method throws an exception if the given value
	 * cannot be represented as a string.
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static final function cast($value): string {
		return is_object($value) && method_exists($value, 'toString')
			? $value->toString() : (string)$value;
	}

	/**
	 * Joins all given arguments into one single string using given separator.
	 *
	 * @param string $separator
	 * @param mixed $source, ...
	 * @return string
	 */
	public final static function join($separator, $source){
		return implode($separator, array_map(function($value){ return Str::cast($value); },
			array_filter(Arr::simplify(array_slice(func_get_args(), 1)))));
	}

	/**
	 * @param string $source
	 * @param int $limit
	 * @return string
	 */
	public final static function tr($source, $limit){
		return $limit > 0 && mb_strlen($source) > $limit
			? preg_replace('/[\s,.!?:;-]+[^\s,.!?:;-]{0,' . max([10, floor($limit / 100 * 20)]) . '}$/u', null,
				mb_substr($source, 0, $limit), 1) : $source;
	}

	/**
	 * @param string $source
	 * @param int $limit
	 * @param string $finalizer
	 * @return string
	 */
	public final static function trf($source, $limit, $finalizer = '...'){
		return self::tr($source, $limit - mb_strlen($finalizer)) . $finalizer;
	}

	/**
	 * @param string $source
	 * @param int $limit
	 * @return string
	 */
	public final static function lim($source, $limit){
		return mb_substr($source, 0, $limit);
	}

	/**
	 * @param string $source
	 * @param int $limit
	 * @param string $separator
	 * @return string
	 */
	public final static function clip($source, $limit, $separator = null){
		$prc = floor(mb_strlen($source) / 100 * 20);
		return $limit > 0 && mb_strlen($source) > $limit ? (self::tr($source, $limit - $prc) . $separator
				. trim(mb_substr($source, mb_strlen($source) - $prc))) : $source;
	}

	/**
	 * @param string $source
	 * @return string
	 */
	public final static function strip($source){
		return preg_replace('/<(?:\/|!doctype\s*)?[a-z0-9_-]+\s*(?:[^>\s]+(?:\s*=\s*(?:' . Reglib::QUOTED
			. '|[^>\s]+))?\s*)*\/?>/i', ' ', preg_replace('/<!--.*?-->/s', null, $source));
	}

	/**
	 * @param string $source
	 * @param string $allow, ...
	 * @return string
	 */
	public final static function stripl($source, $allow = null){
		$allow = array_filter(array_slice(func_get_args(), 1), function($value){
			return preg_match('/^[A-Za-z0-9_-]+$/', $value); });

		return preg_replace_callback('/<((?:\/|!doctype\s*)?[a-z0-9_-]+)\s*(?:[^>\s]+(?:\s*=\s*(?:' . Reglib::QUOTED
			. '|[^>\s]+))?\s*)*\/?>/i', function($value) use ($allow){
				return preg_match('/\s+/', trim($value[1])) > 0 || !in_array(trim($value[1], ' /'), $allow) ? '' : $value[0];
			}, preg_replace('/<!--.*?-->/s', null, $source));
	}

	/**
	 * @param string $source
	 * @return string mixed
	 */
	public final static function gtrim($source){
		return trim(preg_replace('/\s+/', ' ', $source));
	}

	/**
	 * @param string $source
	 * @return string
	 */
	public final static function br2nl($source){
		return preg_replace('/<br *\/?>/', "\n", $source);
	}

	/**
	 * @param string $source
	 * @return mixed
	 */
	public final static function p2nl($source){
		return preg_replace('/<\/(?:p' . (func_num_args() > 1 ? ('|' . implode('|', array_map(function($value){
			return preg_quote(Str::cast(trim(trim($value), '<>')), '/');
		}, array_slice(func_get_args(), 1)))) : null) . ')>/', "\$0\n", $source);
	}

}
