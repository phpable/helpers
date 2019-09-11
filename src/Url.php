<?php
namespace Able\Helpers;

use Able\Helpers\Abstractions\AHelper;

class Url extends AHelper{

	/**
	 * @param string $url
	 * @return array
	 */
	public static final function parse(string $url): array {
		parse_str($url, $Data);
		return $Data;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	public static final function clean(string $url): string {
		return preg_replace('/\?.*$/', '', preg_replace('/#.*$/', '', $url));
	}

	/**
	 * @param string $url
	 * @param int $limit
	 * @return string
	 */
	public static final function tr(string $url, int $limit): string{
		return $limit > 0 && mb_strlen(self::clean($url)) > $limit
			? preg_replace('/\/+[^\/]{0,10}$/u', '', mb_substr($url, 0,
				$limit), 1) : $url;
	}


	/**
	 * @param string $url
	 * @param int $limit
	 * @param string $finalizer
	 * @return string
	 */
	public final static function trf(string $url, int $limit, string $finalizer = '...'): string {
		return $limit > 0 && mb_strlen(self::clean($url)) > $limit
			? self::tr($url, $limit) . $finalizer : $url;
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	public final static function isabs(string $url): bool{
		return preg_match('/^(?:[a-z]+:\/\/)?[a-z0-9-]+\.[a-z]+/i', $url) > 0;
	}

	/**
	 * @param string $url
	 * @param string $left
	 * @return string
	 */
	public final static function abs(string $url, string $left):string {
		return !self::isabs($url) || !preg_match('/^' . preg_quote($left, '/') . '/', $url)
			? preg_replace('/^(?:[a-z]+:\/\/)?(?:[a-z0-9-]+\.[a-z]+)?\/*/', rtrim($left, ' /')
				. '/', $url) : $url;
	}

}
