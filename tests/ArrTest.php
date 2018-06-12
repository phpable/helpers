<?php
namespace Able\Helpers\Tests;

use \PHPUnit\Framework\TestCase;
use \Able\Helpers\Arr;

class ArrTest extends TestCase {

	public final function testSimplify() {
		$arr = ['a', 'b', 'c', [1, 2, 3, ['x' => 'lt_x', 'y' => 'lt_y', 'z' => 'lt_z'],
			4, null, 5, ['x' => '', 'y' => 0, 'z' => null]], 'd', 'e'];

		$this->assertSame(Arr::simplify($arr), ['a', 'b', 'c', 1, 2, 3, 'lt_x', 'lt_y', 'lt_z',
			4, null, 5, '', 0, null, 'd', 'e']);
	}

	public final function testAppend(){
		$arr = ['a' => 'n1', 'b' => 'n2', 'c' => 'n3', 'd' => 'n4', 'e' => 'n5', 'f' => 'n6'];

		$this->assertSame(Arr::append($arr, ['c' => 'n7', 'g' => 'n8', 'h' => 'n9']), ['a' => 'n1', 'b' => 'n2',
			'd' => 'n4', 'e' => 'n5', 'f' => 'n6', 'c' => 'n7', 'g' => 'n8', 'h' => 'n9']);
	}

	public final function testPrepend(){
		$arr = ['c' => 'n4', 'd' => 'n5', 'e' => 'n6', 'f' => 'n7', 'g' => 'n8', 'h' => 'n9'];

		$this->assertSame(Arr::prepend($arr, ['a' => 'n1', 'b' => 'n2', 'g' => 'n3']), ['a' => 'n1', 'b' => 'n2',
			'g' => 'n3', 'c' => 'n4', 'd' => 'n5', 'e' => 'n6', 'f' => 'n7', 'h' => 'n9']);
	}

	public final function testPush(){
		$arr = ['a', 'b', 'c', 'd'];

		$this->assertSame(Arr::push($arr, ['e', ['key1' => 'f', 'key2' => 'g',
			'key3' => ['h', 'i']]]), ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i']);
	}

	public final function testUnshift(){
		$arr = ['f', 'g', 'h', 'i'];

		$this->assertSame(Arr::unshift($arr, ['a', ['key1' => 'b', 'key2' => 'c',
			'key3' => ['d', 'e']]]), ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i']);
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
