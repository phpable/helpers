<?php
namespace Able\Helpers;

use \Able\Helpers\Abstractions\AHelper;

class Env extends AHelper {

	/**
	 * @const string
	 */
	public const EP_WINDOWS = 'windows';

	/**
	 * @const string
	 */
	public const EP_UNIX = 'unix';

	/**
	 * @const string
	 */
	public const EP_UNDEFINED = 'undefined';

	/**
	 * Returns a platform name from possible variants:
	 * "windows", "unix", "undefined".
	 *
	 * @return string
	 */
	public final static function name() : string {
		return match (PHP_SHLIB_SUFFIX) {
			'so' => self::EP_UNIX,
			'dll' => self::EP_WINDOWS,

			/**
			 * Something weird detected.
			 */
			default => self::EP_UNDEFINED,
		};
	}

}
