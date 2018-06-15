<?php
namespace Able\Helper;

use \Able\Helper\Abstractions\AHelper;

class Hash extends AHelper {

	/**
	 * @param int $length
	 * @return string
	 * @throws \Exception
	 */
	public final static function solt($length){
		if ((int)$length > 62){
			throw new \Exception('Max solt length can not be more that 62 characters!');
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
	 * @return bool
	 * @throws \Exception
	 */
	public final static function make($type, $value){
		switch((int)$type){
			case self::HASH_TYPE_MD5:
				return md5(implode(Arr::simplify(array_slice(func_get_args(), 1))));
		}
		throw new \Exception('Unknown validation type "' .  $type . '"!');
	}


	/**
	 * @param string $value
	 * @param int $type
	 * @return bool
	 * @throws \Exception
	 */
	public final static function validate($value, $type){
		switch((int)$type){
			case self::HASH_TYPE_MD5:
				return strlen(($value = strtolower(trim($value)))) == 32 && ctype_xdigit($value);
		}
		throw new \Exception('Unknown validation type "' .  $type . '"!');
	}

}
