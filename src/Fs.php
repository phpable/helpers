<?php
namespace Able\Helpers;

use \Able\Helpers\Abstractions\AHelper;

class Fs extends AHelper {

	/**
	 * @param string $path
	 * @return array
	 */
	public static function files(string $path): array {
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
	public static final function ppath (string $path): string|bool {
		while (!is_dir($path) && strlen($path) > 0){
			$path = preg_replace('/[^\/]+\/?$/', '', $path);
		}

		return !empty($path) ? $path : false;
	}

	/**
	 * @param string $file, ...
	 * @return string
	 */
	public static final function try(string $file): string{
		return Arr::value(array_filter(Arr::simplify(func_get_args()),
			function($value){ return file_exists($value) && !is_dir($value); }));
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public final static function ext(string $path): string {
		return preg_replace('/^.*\./', '', basename($path));
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public static final function hash(string $path): string {
		return preg_replace('/[^\/]+$/', '', $path)
			. md5(basename($path)) . '.' . Fs::ext($path);
	}

	/**
	 * @param string $path
	 * @param string $root
	 * @return string|array|null
	 */
	public static final function normalize(string $path, string $root): string|array|null {
		$path = preg_replace('/^\.\//', $root . '/', $path);

		while(!isset($count) || $count > 0) {
			$path = preg_replace('/[^\/]+\/\.\.\//', '', $path, -1, $count);
		}

		return $path;
	}
}
