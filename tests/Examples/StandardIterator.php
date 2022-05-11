<?php
namespace Able\Helpers\Tests\Examples;

class StandardIterator implements \Iterator {

	/**
	 * @var int
	 */
	private int $position = 0;

	/**
	 * @var array
	 */
	private array $array = [
		"first",
		"second",
		"third",
	];

	public function __construct() {
		$this->position = 0;
	}

	public function rewind(): void {
		$this->position = 0;
	}

	public function current(): mixed {
		return $this->array[$this->position];
	}

	public function key(): mixed {
		return $this->position;
	}

	public function next(): void {
		++$this->position;
	}

	public function valid(): bool {
		return isset($this->array[$this->position]);
	}
}
