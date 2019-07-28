<?php
namespace Able\Helpers\Tests\Examples;

class Arrayable {

	/**
	 * @var array
	 */
	private $array = ['a', 'b',
		'c', 'd', 'e', 'f'];

	/**
	 * @return array
	 */
	public final function toArray(): array {
		return $this->array;
	}
}
