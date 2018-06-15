<?php
namespace Able\Helper;

use \Able\Helper\Abstractions\AHelper;

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
		switch (PHP_SHLIB_SUFFIX){
			case 'so': return self::EP_UNIX;
			case 'dll': return self::EP_WINDOWS;
			default: return self::EP_UNDEFINED;
		}
	}

}
