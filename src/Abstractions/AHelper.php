<?php
namespace Able\Helpers\Abstractions;

abstract class AHelper {

	/**
	 * To avoid circular dependencies this class is not used TUncreatable
	 * and TUnclonable traits from the Able\Prototypes package but de-facto it does!
	 */

	/**
	 * @throws \Exception
	 */
	public final function __construct(){
		throw new \Exception('Can\'t create an uncreatable object!');
	}

	/**
	 * @throws \Exception
	 */
	public final function __clone(){
		throw new \Exception('Can\'t clone an unclonable object!');
	}

}
