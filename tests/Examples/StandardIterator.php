<?php
namespace Able\Helpers\Tests\Examples;

class StandardIterator implements \Iterator {

	/**
	 * @var int
	 */
	private $position = 0;

	/**
	 * @var array
	 */
	private $array = array(
		"first",
		"second",
		"third",
	);

	public function __construct() {
		$this->position = 0;
	}

	public function rewind() {
		$this->position = 0;
	}

	public function current() {
		return $this->array[$this->position];
	}

	public function key() {
		return $this->position;
	}

	public function next() {
		++$this->position;
	}

	public function valid() {
		return isset($this->array[$this->position]);
	}
}
