<?php
namespace Able\Helpers\Tests\Examples;

class Arrayable {

	/**
	 * @var array
	 */
	private $Array = [];

	/**
	 * Arrayable constructor.
	 * @param array $array
	 */
	public final function __construct(array $array = []) {
		$this->Array = $array;
	}

	/**
	 * @return array
	 */
	public final function toArray(): array {
		return $this->array;
	}
}
