<?php
namespace Able\Helpers;

use \Able\Helpers\Abstractions\AHelper;

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
	 * @param mixed ...$args
	 *
	 * @return string
	 *
	 * @throws \Exception
	 */
	public static final function post($url, array $Params = [], array $Headers = [], $flags = 0b0000, ...$args) {
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

		if (($Response = curl_exec($Curl)) === false) {
			$Exception = new \Exception(curl_error($Curl), curl_errno($Curl));
			curl_close($Curl);
			throw $Exception;
		}

		return $Response;
	}

	/**
	 * Send get request and return result.
	 * @param string $url
	 * @param array $Params
	 * @param array $Headers
	 * @param int $flags
	 *
	 * @return string
	 * @throws /Exception
	 */
	public static final function get($url, array $Params = [], array $Headers = [], int $flags = 0b0000, array $Options = []): string {
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
			if (empty($Options['user'] || empty($Options['password']))) {
				throw new \Exception('Invalid credentials!');
			}

			curl_setopt($Curl, CURLOPT_USERPWD, sprintf('%s:%s', $Options['user'], $Options['password']));
		}

		if (($Response = curl_exec($Curl)) === false) {
			$Exception = new \Exception(curl_error($Curl), curl_errno($Curl));
			curl_close($Curl);
			throw $Exception;
		}

		curl_close($Curl);
		return $Response;
	}

}
