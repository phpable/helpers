<?php
namespace Able\Helpers;

use \Able\Helpers\Abstractions\AHelper;
use \Able\Helpers\Arr;

class Jsn extends AHelper {

	/**
	 * Decodes a JSON string into an associative array.
	 *
	 * @attention This method throws an exception
	 * if given value is not a valid JSON string.
	 *
	 * @param string $source
	 * @return array
	 * @throws \Exception
	 */
	public final static function decode(string $source): array {
		if (is_null($source = json_decode($source, true)) && json_last_error() !== JSON_ERROR_NONE){
			throw new \Exception('Invalid json format!');
		}

		return $source;
	}

	/**
	 * Encodes an associative array into its JSON string representation.
	 *
	 * @attention This method throws an exception
	 * if given value cannot be represented as a JSON string.
	 *
	 * @param array $source
	 * @return string
	 * @throws \Exception
	 */
	public final static function encode(array $source): string {
		if (($source = json_encode($source)) == false && json_last_error() !== JSON_ERROR_NONE){
			throw new \Exception('Invalid raw data!');
		}

		return $source;
	}

	/**
	 * Adds the given data into the end of the given JSON string representation.
	 *
	 * @param string $source
	 * @param array $Append
	 * @return string
	 * @throws \Exception
	 */
	public final static function append(string $source, array $Append): string {
		return self::encode(Arr::append(self::decode($source), $Append));
	}

	/**
	 * Adds the given data into the beginning of the given JSON string representation.
	 *
	 * @param string $source
	 * @param array $Prepend
	 * @return string
	 * @throws \Exception
	 */
	public final static function prepend(string $source, array $Prepend): string {
		return self::encode(Arr::prepend(self::decode($source), $Prepend));
	}

	/**
	 * Removes an element from the given JSON string representation.
	 *
	 * @see Arr::clear()
	 *
	 * @param string $source
	 * @param mixed $keys, ...
	 * @return string
	 * @throws \Exception
	 */
	public final static function clear(string $source, $keys): string {
		return self::encode(Arr::erase(self::decode($source),
			...array_slice(func_get_args(), 1)));
	}

	/**
	 * Adds an element into the given JSON string representation.
	 *
	 * @see Arr::improve()
	 *
	 * @param string $source
	 * @param $keys
	 * @param $value
	 * @return string
	 * @throws \Exception
	 */
	public final static function improve(string $source, $keys, $value): string {
		return self::encode(Arr::improve(self::decode($source),
			...array_slice(func_get_args(), 1)));
	}

	/**
	 * Merge an array with the given JSON string representation.
	 *
	 * @param string $source
	 * @param array $Values
	 * @return string
	 * @throws \Exception
	 */
	public final static function merge(string $source, array $Values){
		return self::encode(array_merge(self::decode($source), $Values));
	}

	/**
	 * Returns a single element from a JSON array by its key.
	 *
	 * @param string $source
	 * @param $key
	 * @param $default
	 * @return mixed
	 * @throws \Exception
	 */
	public final static function get(string $source, $key, $default = null){
		return Arr::get(self::decode($source), $key, $default);
	}

}
