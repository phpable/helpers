<?php
namespace Able\Helper;

use \Able\Helper\Arr;
use \Able\Helper\Abstractions\AHelper;

class Fs extends AHelper {

	/**
	 * @param string $path
	 * @return array
	 */
	public static function files($path) {
		return Arr::simplify(array_map(function($value){
			return $value[strlen($value) - 1] == '/' ? [Fs::files($value) ] : $value; },
				glob(rtrim($path, '/') . '/*', GLOB_NOSORT | GLOB_MARK)));
	}

	/**
	 * Returns first existing parent directory from given path.
	 *
	 * @param string $path
	 * @return string|bool
	 */
	public static final function ppath ($path){
		while (!is_dir($path) && strlen($path) > 0){
			$path = preg_replace('/[^\/]+\/?$/', null, $path);
		}

		return !empty($path) ? $path : false;
	}

	/**
	 * @param string $file, ...
	 * @return string
	 */
	public static final function try($file){
		return Arr::val(array_filter(Arr::simplify(func_get_args()),
			function($value){ return file_exists($value) && !is_dir($value); }));
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public final static function ext(string $path){
		return preg_replace('/^.*\./', '', basename($path));
	}

	/**
	 * @param $path
	 * @return string
	 */
	public static final function hash($path){
		return preg_replace('/[^\/]+$/', '', $path)
			. md5(basename($path)) . '.' . Fs::ext($path);
	}

}
