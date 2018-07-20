<?php
namespace Able\Helpers;

use \Able\Reglib\Reglib;
use \Able\Reglib\Regexp;

use \Able\Helpers\Arr;
use \Able\Helpers\Abstractions\AHelper;

class Src extends AHelper{

	/**
	 * Convert any separated string to camel case.
	 *
	 * @param $string
	 * @param string $separator, ...
	 * @return string
	 */
	public static final function tcm($string, $separator = '_') {
		return implode(null, array_map('ucfirst', preg_split('/' . implode('|', array_map(function($separator){
			return preg_quote($separator, '/');
		}, array_pad(array_slice(func_get_args(), 1), 1, '_')))  . '/', trim(strtolower($string)), -1, PREG_SPLIT_NO_EMPTY)));
	}

	/**
	 * Convert any string from camel case to a string with a given delimiter.
	 *
	 * @param string $string
	 * @param string $separator
	 * @return string
	 */
	public static final function fcm($string, $separator = '_') {
		return ltrim(preg_replace_callback('/\\\?[A-Z][A-Za-z0-9]+?/', function($Macthes) use ($separator){
			return $separator . ltrim(strtolower($Macthes[0]), '\\');
		}, $string), $separator);
	}

	/**
	 * Convert any separated string to qualifier name.
	 *
	 * @param $string
	 * @param string $separator, ...
	 * @return string
	 */
	public static final function tons($string, $separator = '.') {
		return implode('\\', array_map('ucfirst', preg_split('/' . implode('|', array_map(function($separator){
			return preg_quote($separator, '/');
		}, array_pad(array_slice(func_get_args(), 1), 1, '.')))  . '/', trim(strtolower($string)), -1, PREG_SPLIT_NO_EMPTY)));
	}

	/**
	 * Convert any qualifier name to a string with a given delimiter.
	 *
	 * @param string $string
	 * @param string $separator
	 * @return string
	 */
	public static final function frns($string, $separator = '.') {
		return implode($separator, preg_split('/\\\/', trim(strtolower($string)), -
			1, PREG_SPLIT_NO_EMPTY));
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public static final function lns($string){
		return preg_replace('/\\\\[^\\\\]+$/', null, rtrim($string, '\\'));
	}

	/**
	 * @param string $string
	 * @return string
	 */
	public static final function rns($string){
		return preg_replace('/^.*\\\\/', null, rtrim($string, '\\'));
	}

	/**
	 * @param string $source
	 * @param string $fragments
	 * @return string
	 */
	public static final function esc(string $source, string $fragments): string {
		return preg_replace('/(?<=\A|[^\\\\])(' . implode('|', array_map(function($value){
			return preg_quote($value, '/'); }, str_split($fragments, 1))). ')/', '\\\$1', $source);
	}

	/**
	 * @param mixed $Target
	 * @param array $Params
	 * @param null $default
	 * @return mixed|null
	 * @throws \Exception
	 */
	public static final function call($Target, $Params = [], $default = null){
		if (is_callable($Target)) {
			return call_user_func_array($Target, Arr::cast($Params));
		}

		if (is_callable($default)) {
			return call_user_func_array($default, (Arr::cast($Params)));
		}

		return $default;
	}

	/**
	 * @param string $target
	 * @return object
	 * @throws \Exception
	 */
	public static final function make(string $target){
		if (!class_exists($target)){
			throw new \Exception('Undefined target class!');
		}

		return new $target(...array_slice(func_get_args(), 1));
	}
}

