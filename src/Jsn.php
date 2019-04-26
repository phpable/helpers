<?php
namespace Able\Helpers;

use \Able\Helpers\Abstractions\AHelper;
use \Able\Helpers\Arr;

use \Exception;

class Jsn extends AHelper {

	/**
	 * Decodes a JSON-packed string into an associative array.
	 *
	 * @attention Throws an exception if the given string
	 * is not a valid JSON string.
	 *
	 * @param string $source
	 * @return array
	 *
	 * @throws Exception
	 */
	public final static function decode(string $source): array {
		if (is_null($source = json_decode($source, true))
			|| json_last_error() !== JSON_ERROR_NONE){

				throw new Exception('Invalid json format!');
		}

		return $source;
	}

	/**
	 * Packs the given data into its JSON string.
	 *
	 * @attention Throws an exception if the given data be represented as a JSON string.
	 * if given value cannot be represented as a JSON string.
	 *
	 * @param array $source
	 * @return string
	 *
	 * @throws Exception
	 */
	public final static function encode(array $source): string {
		if (($source = json_encode($source)) == false
			|| json_last_error() !== JSON_ERROR_NONE){

				throw new Exception('Invalid raw data!');
		}

		return $source;
	}

	/**
	 * Adds the given data into the end of the JSON string.
	 *
	 * @param string $source
	 * @param array $Append
	 * @return string
	 *
	 * @throws Exception
	 */
	public final static function append(string $source, array $Append): string {
		return self::encode(Arr::append(self::decode($source), $Append));
	}

	/**
	 * Adds the given data into the beginning of the JSON string.
	 *
	 * @param string $source
	 * @param array $Prepend
	 * @return string
	 *
	 * @throws Exception
	 */
	public final static function prepend(string $source, array $Prepend): string {
		return self::encode(Arr::prepend(self::decode($source), $Prepend));
	}

	/**
	 * Erases elements from the given JSON string
	 * representation following the given path.
	 *
	 * @see Arr::erase()
	 *
	 * @param string $source
	 * @param mixed ...$args
	 * @return string
	 * @throws Exception
	 */
	public final static function erase(string $source, ...$args): string {
		return self::encode(Arr::erase(self::decode($source), ...$args));
	}

	/**
	 * Adds an element into the JSON collection following the given path.
	 *
	 * @see Arr::improve()
	 *
	 * @param string $source
	 * @param mixed ...$args
	 * @return string
	 *
	 * @throws Exception
	 */
	public final static function improve(string $source, ...$args): string {
		return self::encode(Arr::improve(self::decode($source), ...$args));
	}

	/**
	 * Unite the JSON collection and the given data into a new JSON collection.
	 *
	 * @see Arr::unite()
	 *
	 * @param string $source
	 * @param array $Data
	 * @return string
	 *
	 * @throws Exception
	 */
	public final static function unite(string $source, array $Data): string {
		return self::encode(Arr::unite(self::decode($source), $Data));
	}

	/**
	 * Tries to retrieve a single value or a subset
	 * from a JSON collection using the given path.
	 *
	 * @see Arr::follow()
	 *
	 * @param string $source
	 * @param mixed ...$args
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public final static function follow(string $source, string ...$args) {
		return Arr::follow(self::decode($source), ...$args);
	}

	/**
	 * Returns a single element from a JSON array by its key.
	 *
	 * @see Arr::get()
	 *
	 * @param string $source
	 * @param $key
	 * @param $default
	 * @return mixed
	 *
	 * @throws Exception
	 */
	public final static function get(string $source, $key, $default = null) {
		return Arr::get(self::decode($source), $key, $default);
	}

}
