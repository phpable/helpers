<?php
namespace Able\Helpers\Tests;

use \PHPUnit\Framework\TestCase;

use \Able\Helpers\Jsn;
use \Able\Helpers\Arr;

use \Exception;

class JsnTest extends TestCase {

	/**
	 * @throws Exception
	 */
	public final function testEncodeDecode() {
		$arr = [1 => 'string', 2 => 2.50, 3 =>['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'],
			'testlongstring' => 'very-very-very "long" string!', null, false, true, ''];

		$this->assertEquals(Jsn::decode(Jsn::encode($arr)), $arr);
	}

	/**
	 * @throws Exception
	 */
	public final function testAppend() {
		$arr1 = [1 => 'string', 2 => 2.50, 3 =>['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'],
			'testlongstring' => 'very-very-very "long" string!', null, false, true, ''];

		$arr2 = ['222' => 'new_string'];

		$json = Jsn::encode($arr1);

		$this->assertSame(Jsn::append($json, $arr2), json_encode(Arr::append($arr1, $arr2)));
	}


	/**
	 * @throws Exception
	 */
	public final function testPrepend() {
		$arr1 = [1 => 'string', 2 => 2.50, 3 =>['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'],
			'testlongstring' => 'very-very-very "long" string!', null, false, true, ''];

		$arr2 = ['222' => 'new_string'];

		$json = Jsn::encode($arr1);

		$this->assertSame(Jsn::prepend($json, $arr2), json_encode(Arr::prepend($arr1, $arr2)));
	}

	/**
	 * @throws Exception
	 */
	public final function testImprove() {
		$arr = [1 => 'string', 2 => 2.50, 3 => ['a' => [1, 2, 3, 4],
			'b' => ['green', 'yellow'], 'c' => ['one', 'two', 'three']]];


		$json = Jsn::encode($arr);
		$this->assertSame(Jsn::improve($json, '3', 'b', 'pink'), json_encode(Arr::improve($arr, '3', 'b', 'pink')));
	}

	/**
	 * @throws Exception
	 */
	public final function testErase() {
		$arr = [1 => 'string', 2 => 2.5, 3 =>['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'],
			'testlongstring' => 'very-very-very "long" string!', 4 => null, 5 => false, 6 => true, 7 => ''];

		$this->assertEquals(Jsn::decode(Jsn::erase(Jsn::encode($arr), 'testlongstring')),
			Arr::erase($arr, 'testlongstring'));

		$this->assertEquals(Jsn::decode(Jsn::erase(Jsn::encode($arr), 3)),
			Arr::erase($arr, 3));
	}

}
