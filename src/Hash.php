<?php
namespace Able\Helpers;

use \Able\Helpers\Abstractions\AHelper;

use \Exception;

class Hash extends AHelper {

	/**
	 * @param int $length
	 * @return string
	 *
	 * @throws Exception
	 */
	public final static function solt(int $length): string{
		if ($length > 62){
			throw new Exception('Max solt length can not be more that 62 characters!');
		}

		return substr(str_shuffle(implode(array_merge(range('a', 'z'), range('A', 'Z'), range('0', '9')))), 0, $length);
	}

	/**
	 * @const int
	 */
	const HASH_TYPE_MD5 = 1;

	/**
	 * @param int $type
	 * @param string $value
	 * @return string
	 *
	 * @throws Exception
	 */
	public final static function make(int $type, string $value): string {
		switch($type){
			case self::HASH_TYPE_MD5:
				return md5(implode(Arr::simplify(array_slice(func_get_args(), 1))));
		}

		throw new Exception('Unknown hash type: ' .  $type . '!');
	}


	/**
	 * @param string $value
	 * @param int $type
	 * @return bool
	 *
	 * @throws Exception
	 */
	public final static function validate(string $value, int $type): bool {
		switch($type){
			case self::HASH_TYPE_MD5:
				return strlen(($value = strtolower(trim($value)))) == 32 && ctype_xdigit($value);
		}

		throw new Exception('Unknown hash type: ' .  $type . '!');
	}
}
