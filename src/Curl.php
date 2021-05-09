<?php
namespace Able\Helpers;

use \Able\Helpers\Abstractions\AHelper;
use \Exception;

class Curl extends AHelper{

	/**
	 * @const int
	 */
	const F_RETURN_HEADERS = 0b0001;

	/**
	 * @const int
	 */
	const F_BASE_AUTH = 0b0010;

	/**
	 * Send post request and return result.
	 * @param string $url
	 * @param array $Params
	 * @param array $Headers
	 * @param int $flags
	 * @param string ...$Options
	 *
	 * @return string
	 *
	 * @throws Exception
	 */
	public static final function post(string $url, array $Params = [], array $Headers = [], int $flags = 0b0000, ?string ...$Options): string {
		$Curl = curl_init();
		if (preg_match('/^(.*):([0-9]+)$/', $url, $Macth) > 0){
			curl_setopt($Curl, CURLOPT_PORT, $Macth[2]);
			$url = $Macth[1];
		}

		curl_setopt($Curl, CURLOPT_URL, $url);
		curl_setopt($Curl, CURLOPT_POST, 1);
		if (count($Params) > 0){
			curl_setopt($Curl, CURLOPT_POSTFIELDS, http_build_query($Params));
		}

		if (count($Headers) > 0){
			curl_setopt($Curl, CURLOPT_HTTPHEADER, Arr::pack($Headers, ': '));
		}

		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($Curl, CURLOPT_SSL_VERIFYHOST, 2);

		if (self::F_RETURN_HEADERS & $flags) {
			curl_setopt($Curl, CURLOPT_HEADER, true);
		}

		if (self::F_BASE_AUTH & $flags) {
			if (is_null($Options) || count($Options) < 2) {
				throw new Exception('Invalid credentials!');
			}

			curl_setopt($Curl, CURLOPT_USERPWD, sprintf('%s:%s', array_shift($Options), array_shift($Options)));
		}

		try {
			if (($Response = curl_exec($Curl)) === false) {
				throw new Exception(curl_error($Curl), curl_errno($Curl));
			}
		} finally {
			curl_close($Curl);
		}

		return $Response;
	}

	/**
	 * Send get request and return result.
	 * @param string $url
	 * @param array $Params
	 * @param array $Headers
	 * @param string ...$Options
	 * @param int $flags
	 *
	 * @return string
	 * @throws Exception
	 */
	public static final function get(string $url, array $Params = [], array $Headers = [], int $flags = 0b0000, ?string ...$Options): string {
		$Curl = curl_init();
		if (preg_match('/^(.*):([0-9]+)$/', $url, $Macth) > 0){
			curl_setopt($Curl, CURLOPT_PORT, $Macth[2]);
			$url = $Macth[1];
		}

		if (count($Params) > 0){
			$url .= '?' . http_build_query($Params, '', '&', PHP_QUERY_RFC3986);
		}

		if (count($Headers) > 0){
			curl_setopt($Curl, CURLOPT_HTTPHEADER, Arr::pack($Headers, ': '));
		}

		curl_setopt($Curl, CURLOPT_URL, $url);
		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($Curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($Curl, CURLOPT_SSL_VERIFYHOST, 2);

		if (self::F_RETURN_HEADERS & $flags) {
			curl_setopt($Curl, CURLOPT_HEADER, true);
		}

		if (self::F_BASE_AUTH & $flags) {
			if (is_null($Options) || count($Options) < 2) {
				throw new Exception('Invalid credentials!');
			}

			curl_setopt($Curl, CURLOPT_USERPWD, sprintf('%s:%s', array_shift($Options), array_shift($Options)));
		}

		try {
			if (($Response = curl_exec($Curl)) === false) {
				throw new Exception(curl_error($Curl), curl_errno($Curl));
			}
		} finally {
			curl_close($Curl);
		}

		return $Response;
	}
}
