<?php
namespace Able\Helpers;

use \Able\Helpers\Abstractions\AHelper;

class Url extends AHelper{

	/**
	 * @param string $url
	 * @return array
	 */
	public static final function parse($url) {
		parse_str($url, $Data);
		return $Data;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public static final function clean($url){
		return preg_replace('/\?.*$/', null, preg_replace('/#.*$/',
			null, $url));
	}

	/**
	 * @param string $url
	 * @param int $limit
	 * @return string
	 */
	public static final function tr($url, $limit){
		return $limit > 0 && mb_strlen($source = self::clean($url)) > $limit
			? preg_replace('/\/+[^\/]{0,10}$/u', null, mb_substr($url, 0,
				$limit), 1) : $url;
	}


	/**
	 * @param string $url
	 * @param int $limit
	 * @param string $finalizer
	 * @return string
	 */
	public final static function trf($url, $limit, $finalizer = '...'){
		return $limit > 0 && mb_strlen($source = self::clean($url)) > $limit
			? self::tr($url, $limit) . $finalizer : $url;
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	public final static function isabs($url){
		return preg_match('/^(?:[a-z]+:\/\/)?[a-z0-9-]+\.[a-z]+/i', $url) > 0;
	}

	/**
	 * @param string $url
	 * @param string $left
	 * @return string
	 */
	public final static function abs($url, $left){
		return !self::isabs($url) || !preg_match('/^' . preg_quote($left, '/') . '/', $url)
			? preg_replace('/^(?:[a-z]+:\/\/)?(?:[a-z0-9-]+\.[a-z]+)?\/*/', rtrim($left, ' /')
				. '/', $url) : $url;
	}

}
