<?php
namespace Able\Helpers\Tests;

use \Able\Helpers\Arr;
use \Able\Helpers\Tests\Examples\Arrayable;
use \Able\Helpers\Tests\Examples\TestClassA;
use \Able\Helpers\Tests\Examples\StandardIterator;

use \PHPUnit\Framework\TestCase;

use \ArrayObject;
use \Generator;

class ArrTest extends TestCase {

	public final function testCastable() {
		$arr = ['a', 'b', 'c', 'd'];
		$this->assertTrue(Arr::castable($arr));

		$Obj = new Arrayable($arr);
		$this->assertTrue(Arr::castable($Obj));

		$obj = new ArrayObject($arr);
		$this->assertTrue(Arr::castable($obj));
		$this->assertTrue(Arr::castable($obj->getIterator()));

		$v = "test string";
		$this->assertFalse(Arr::castable($v));

		$v = null;
		$this->assertFalse(Arr::castable($v));

		$v = 0;
		$this->assertFalse(Arr::castable($v));
	}

	public final function testCast() {
		$arr = ['a' => 'a1', 'b' => 'b2', 'c' => 'c2', 'd' => 'd3'];
		$this->assertSame(Arr::cast($arr), $arr);

		$Obj = new Arrayable($arr);
		$this->assertTrue(Arr::castable($Obj));

		$obj = new ArrayObject($arr);
		$this->assertSame(Arr::cast($obj), $arr);
		$this->assertSame(Arr::cast($obj->getIterator()), $arr);


		$v = "test string";
		$this->assertSame(Arr::cast($v), [$v]);

		$v = null;
		$this->assertSame(Arr::cast($v), []);

		$v = 0;
		$this->assertSame(Arr::cast($v), [0]);
	}


	public final function testCollect() {
		$arr1 = ['a' => 'a!', 'b' => 'b!', 'c' => 'c!'];

		$arr2 = [22 => '%', 'key22' => 19,
			'test1' => ['test1#k1' => 'test1#el1', 'test1#k2' => 'test1#el2'],
			['test2#k1' => 'test2#el1', 'test2#k2' => 'test2#el2']
		];

		$this->assertSame(Arr::collect($arr1, 1000, 'test string!', $arr2), [
			'a' => 'a!', 'b' => 'b!', 'c' => 'c!',
			0 => 1000,
			1 => 'test string!',
			2 => '%',
			'key22' => 19,
			'test1' => ['test1#k1' => 'test1#el1', 'test1#k2' => 'test1#el2'],
			3 => ['test2#k1' => 'test2#el1', 'test2#k2' => 'test2#el2'],
		]);
	}

	public final function testSimplify() {
		$arr = ['a', 'b', 'c', [1, 2, 3, ['x' => 'lt_x', 'y' => 'lt_y', 'z' => 'lt_z'],
			4, null, 5, ['x' => '', 'y' => 0, 'z' => null]], 'd', 'e'];

		$this->assertSame(Arr::simplify($arr), ['a', 'b', 'c', 1, 2, 3, 'lt_x', 'lt_y', 'lt_z',
			4, 5, '', 0, 'd', 'e']);

		$arr = [null, null, null];
		$this->assertSame(Arr::simplify($arr), []);

		$v = null;
		$this->assertSame(Arr::simplify($v), []);

		$v = 0;
		$this->assertSame(Arr::simplify($v), [0]);

		$this->assertSame(Arr::simplify(['a', 'b', 'c'],
			null, 0, ['k' => [1, 2, 3]]), ['a', 'b', 'c', 0, 1, 2, 3]);
	}


	public final function testIterate(){
		$arr1 = ['a', 'b', 'c', 'd'];
		$arr2 = ['a' => 'n1', 'b' => 'n2', 'c' => 'n3'];
		$arr3 = ['a' => 'n4', 'b' => 'n5', 'c' => 'n6'];

		$this->assertSame(iterator_to_array(Arr::iterate($arr1, $arr2, $arr3)), [
			'a', 'b', 'c', 'd', 'n1', 'n2', 'n3', 'n4', 'n5', 'n6']);
	}


	public final function testAppend(){
		$arr1 = ['a' => 'n1', 'b' => 'n2', 'c' =>
			'n3', 'd' => 'n4', 'e' => 'n5', 'f' => 'n6'];

		$arr2 = ['c' => 'n7', 'g' => 'n8', 'h' => 'n9'];

		$arr3 = [null, 'o', 'e'];
		$arr4 = [1, 2, 3];

		$tmp1 = Arr::append($arr1, $arr2);
		$this->assertSame($tmp1, ['a' => 'n1', 'b' => 'n2', 'd' => 'n4', 'e' => 'n5',
			'f' => 'n6', 'c' => 'n7', 'g' => 'n8', 'h' => 'n9']);

		$tmp2 = Arr::append($tmp1, $arr3);
		$this->assertSame($tmp2, ['a' => 'n1', 'b' => 'n2', 'd' => 'n4', 'e' => 'n5',
			'f' => 'n6', 'c' => 'n7', 'g' => 'n8', 'h' => 'n9', 0 => null, 1 => 'o', 2 => 'e']);


		$tmp3 = Arr::append($tmp2, $arr4);
		$this->assertSame($tmp3, ['a' => 'n1', 'b' => 'n2', 'd' => 'n4', 'e' => 'n5',
			'f' => 'n6', 'c' => 'n7', 'g' => 'n8', 'h' => 'n9', 0 => null, 1 => 'o', 2 =>
			'e', 3 => 1, 4 => 2, 5 => 3]);
	}

	public final function testPrepend(){
		$arr1 = ['a' => 'n1', 'b' => 'n2', 'c' =>
			'n3', 'd' => 'n4', 'e' => 'n5', 'f' => 'n6'];

		$arr2 = ['c' => 'n7', 'g' => 'n8', 'h' => 'n9'];

		$arr3 = [null, 'o', 'e'];
		$arr4 = [1, 2, 3];

		$tmp1 = Arr::prepend($arr1, $arr2);
		$this->assertSame($tmp1, ['c' => 'n7', 'g' => 'n8', 'h' => 'n9',
			'a' => 'n1', 'b' => 'n2', 'd' => 'n4', 'e' => 'n5', 'f' => 'n6']);

		$tmp2 = Arr::prepend($tmp1, $arr3);
		$this->assertSame($tmp2, [
			0 => null, 1 => 'o', 2 => 'e',
			'c' => 'n7', 'g' => 'n8', 'h' => 'n9',
			'a' => 'n1', 'b' => 'n2', 'd' => 'n4', 'e' => 'n5', 'f' => 'n6']);

		$tmp3 = Arr::prepend($tmp2, $arr4);
		$this->assertSame($tmp3, [
			0 => 1, 1 => 2, 2 => 3,
			3 => null, 4 => 'o', 5 => 'e',
			'c' => 'n7', 'g' => 'n8', 'h' => 'n9',
			'a' => 'n1', 'b' => 'n2', 'd' => 'n4', 'e' => 'n5', 'f' => 'n6']);
	}

	public final function testPush(){
		$arr = ['a' => 'a!', 'b' => 'b!', 'c' => 'c!'];

		$this->assertSame(Arr::push($arr, 'd!', 10, ['key1' => 1000, 'key2' => 'test string!']), [
			'a' => 'a!', 'b' => 'b!', 'c' => 'c!', 0 => 'd!', 1 => 10, 2 => [
				'key1' => 1000, 'key2' => 'test string!']]);
	}

	public final function testUnshift(){
		$arr = ['a' => 'a!', 'b' => 'b!', 'c' => 'c!'];

		$this->assertSame(Arr::unshift($arr, 'd!', 10, ['key1' => 1000, 'key2' => 'test string!']), [
			0 => ['key1' => 1000, 'key2' => 'test string!'],
			1 => 10,
			2 => 'd!',
			'a' => 'a!', 'b' => 'b!', 'c' => 'c!']);
	}

	public final function testInsert() {
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertSame(Arr::insert($arr, 2, ['b2' => 'lt_b2']), [
			'a' => 'lt_a', 'b' => 'lt_b',
			'b2' => 'lt_b2',
			'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e', 'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i']);

		$this->assertSame(Arr::insert($arr, 4, 'lt_i0', 'lt_i1'), [
			'a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d',
			0 => 'lt_i0', 1 => 'lt_i1',
			'e' => 'lt_e', 'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i']);

		$this->assertSame(Arr::insert($arr, -4, 'lt_i0', 'lt_i1'), [
			0 => 'lt_i0', 1 => 'lt_i1',
			'a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d',
			'e' => 'lt_e', 'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i']);

		$this->assertSame(Arr::insert($arr, 99, ['b2' => 'lt_b2'], ['c3' => 'lt_c3']), [
			'a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e', 'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i',
			'b2' => 'lt_b2', 'c3' => 'lt_c3']);
	}

	public final function testOnly(){
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertSame(Arr::only($arr, ['a', 'e', 'i']), ['a' => 'lt_a',
			'e' => 'lt_e', 'i' => 'lt_i']);
	}

	public final function testExcept(){
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertSame(Arr::except($arr, ['b', 'c', 'd', 'f', 'g', 'h']), ['a' => 'lt_a',
			'e' => 'lt_e', 'i' => 'lt_i']);
	}

	public final function testHas(){
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertTrue(Arr::has($arr, 'a', ['f', ['d']]));
		$this->assertNotTrue(Arr::has($arr, 'a', ['r', ['d']]));
	}

	public final function testContains(){
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertTrue(Arr::contains($arr, 'lt_a', ['lt_f', ['lt_d']], 'lt_c'));
		$this->assertNotTrue(Arr::contains($arr, 'lt_a', ['lt_f', ['lt_z']], 'lt_cd'));
	}

	public final function testEven(){
		$arr = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];

		$this->assertSame(Arr::even($arr), ['b', 'd', 'f', 'h']);
	}

	public final function testOdd(){
		$arr = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];

		$this->assertSame(Arr::odd($arr), ['a', 'c', 'e', 'g', 'i']);
	}

	public final function testShuffle(){
		$arr = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];

		$this->assertEquals(count(Arr::shuffle($arr)), count($arr));
		$this->assertNotEquals(Arr::shuffle($arr), count($arr));
	}

	public final function testRand(){
		$arr = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];

		for ($i = 0; $i < 10; $i++) {
			$this->assertTrue(in_array(Arr::rand($arr), $arr));
		}
	}

	public final function testLeft(){
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertSame(Arr::left($arr, 'c'), ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c']);

		$this->assertSame(Arr::left($arr, 'h'), ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c',
			'd' => 'lt_d', 'e' => 'lt_e', 'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h']);

		$this->assertSame(Arr::left($arr, 'w'), $arr);
	}

	public final function testRight(){
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertSame(Arr::right($arr, 'c'), ['c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
					'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i']);

		$this->assertSame(Arr::right($arr, 'h'), ['h' => 'lt_h', 'i' => 'lt_i']);

		$this->assertSame(Arr::right($arr, 'w'), $arr);
	}

	public final function testCombine(){
		$arr1 = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];
		$arr2 = ['lt_a', 'lt_b', 'lt_c', 'lt_d', 'lt_e', 'lt_f', 'lt_g'];

		$this->assertSame(Arr::combine($arr1, $arr2, '@'), ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c',
			'd' => 'lt_d', 'e' => 'lt_e', 'f' => 'lt_f', 'g' => 'lt_g', 'h' => '@', 'i' => '@']);
	}

	public final function testCompile(){
		$arr = ['a', 'lt_a', 'b', 'lt_b', 'c', 'lt_c', 'd', 'lt_d', 'e', 'lt_e',
			'f', 'lt_f', 'g', 'lt_g', 'h', 'lt_h', 'i', 'lt_i'];

		$this->assertSame(Arr::compile($arr), ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i']);
	}

	public final function testEach(){
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertSame(Arr::each($arr, function($key, $value){
			return ':' . $value . ':'; }, 'b', 'c', 'd', 'z'), ['a' => 'lt_a', 'b' => ':lt_b:', 'c' => ':lt_c:',
				'd' => ':lt_d:', 'e' => 'lt_e', 'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i']);

		$this->assertSame(Arr::each($arr, function($key, $value){
			return ':' . $value . ':'; }), ['a' => ':lt_a:', 'b' => ':lt_b:', 'c' => ':lt_c:', 'd' => ':lt_d:',
				'e' => ':lt_e:', 'f' => ':lt_f:', 'g' => ':lt_g:', 'h' => ':lt_h:', 'i' => ':lt_i:']);
	}

	public final function testMake() {
		$arr = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];

		$this->assertSame(Arr::make($arr, function($value){
			return 'lt_' . $value; }), ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d',
				'e' => 'lt_e', 'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i']);
	}

	public final function testPack() {
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertSame(Arr::pack($arr, '='), ['a=lt_a', 'b=lt_b', 'c=lt_c', 'd=lt_d', 'e=lt_e',
			'f=lt_f', 'g=lt_g', 'h=lt_h', 'i=lt_i']);
	}

	public final function testUnpack() {
		$arr = ['lt_a', 'b=lt_b', 'c=lt_c', 'd=lt_d', 'e=lt_e',
			'f=lt_f', 'g=lt_g', '=lt_h', '=lt_i'];

		$this->assertSame(Arr::unpack($arr, '='), ['lt_a' => '', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
					'f' => 'lt_f', 'g' => 'lt_g', '' => 'lt_i']);
	}

	public final function testGet() {
		$arr1 = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$arr2 = [0 => 'lt_a', 1 => 'lt_b', 2 => 'lt_c', 3 => 'lt_d', 4 => 'lt_e',
			5 => 'lt_f', 6 => 'lt_g', 7 => 'lt_h', 8 => 'lt_i'];

		$this->assertEquals(Arr::get($arr1, 'a'), 'lt_a');
		$this->assertEquals(Arr::get($arr2, 0), 'lt_a');

		$this->assertEquals(Arr::get($arr1, 'g'), 'lt_g');
		$this->assertEquals(Arr::get($arr2, 6), 'lt_g');

		$this->assertEquals(Arr::get($arr1, 'z'), null);
		$this->assertEquals(Arr::get($arr1, 'z', 'lt_z'), 'lt_z');

		$this->assertEquals(Arr::get($arr2, -1), null);
		$this->assertEquals(Arr::get($arr2, -1, 'undefined'), 'undefined');
	}

	public final function testValue() {
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertEquals(Arr::value($arr, 2), 'lt_c');
		$this->assertEquals(Arr::value($arr, 6), 'lt_g');

		$this->assertEquals(Arr::value($arr, -1), null);
		$this->assertEquals(Arr::value($arr, -1, 'default'), 'default');
	}

	public final function testFirst() {
		$arr = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];

		$this->assertEquals(Arr::first($arr), 'a');
	}

	public final function testLast() {
		$arr = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'];

		$this->assertEquals(Arr::last($arr), 'i');
	}

	public final function testImprove() {
		$arr = ['a' => ['e' => 100, 'g' => ['n1' => '1a', 'n2' => '1b']], 'b' => 12];

		$arr = Arr::improve($arr, 'a', 'g', 'n1', '2b');
		$this->assertSame($arr, ['a' => ['e' => 100,
			'g' => ['n1' => ['1a', '2b'], 'n2' => '1b']], 'b' => 12]);

		$arr = Arr::improve($arr, 'a', 'g', 'n2', '2b');
		$this->assertSame($arr, ['a' => ['e' => 100,
			'g' => ['n1' => ['1a', '2b'], 'n2' => ['1b', '2b']]], 'b' => 12]);

		$arr = Arr::improve($arr, 'a', 'g', 'test');
		$this->assertSame($arr, ['a' => ['e' => 100,
			'g' => ['n1' => ['1a', '2b'], 'n2' => ['1b', '2b'], 0 => 'test']], 'b' => 12]);
	}

	public final function testClear(){
		$arr = ['a' => ['e' => 100, 'g' => ['n1' => '1a', 'n2' => '1b']], 'b' => 12];

		$this->assertSame(Arr::clear($arr, 'a', 'g', 'n1'), ['a' => ['e' => 100,
			'g' => ['n2' => '1b']], 'b' => 12]);

		$this->assertSame(Arr::clear($arr, 'a', 'g'), ['a' => ['e' => 100],
			'b' => 12]);
	}

	public final function testSelect() {
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertEquals(Arr::select($arr, 'lt_b', 'lt_i'), ['b' => 'lt_b', 'i' => 'lt_i']);
	}

	public final function testLike() {
		$arr = ['a' => 'lt_a', 'b' => 'lt_b', 'c' => 'lt_c', 'd' => 'lt_d', 'e' => 'lt_e',
			'f' => 'lt_f', 'g' => 'lt_g', 'h' => 'lt_h', 'i' => 'lt_i'];

		$this->assertSame(Arr::like($arr, 'c', 'b', 'i'), ['c' => 'lt_c', 'b' => 'lt_b', 'i' => 'lt_i']);
		$this->assertSame(Arr::like($arr, 'h', 'e', 'g'), ['h' => 'lt_h', 'e' => 'lt_e', 'g' => 'lt_g']);
		$this->assertSame(Arr::like($arr, 'a', 'h', 'z'), ['a' => 'lt_a', 'h' => 'lt_h']);
	}

}
