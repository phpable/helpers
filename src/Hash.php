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
	const HASH_ALGORITHM_MD5 = 1;

	/**
	 * @param int $algorithm
	 * @param string $value
	 * @return string
	 *
	 * @throws Exception
	 */
	public final static function make(int $algorithm, string $value): string {
		return match ($algorithm) {

			self::HASH_ALGORITHM_MD5
				=> md5(implode(Arr::simplify(array_slice(func_get_args(), 1)))),

			default => throw new Exception(sprintf('Undefined algorithm: %s!', $algorithm)),
		};
	}


	/**
	 * @param string $value
	 * @param int $algorithm
	 * @return bool
	 *
	 * @throws Exception
	 */
	public final static function validate(string $value, int $algorithm): bool {
		return match ($algorithm) {

			self::HASH_ALGORITHM_MD5
				=> strlen(($value = strtolower(trim($value)))) == 32 && ctype_xdigit($value),

			default => throw new Exception(sprintf('Undefined algorithm: %s!', $algorithm)),
		};
	}
}
