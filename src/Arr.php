<?php
namespace Able\Helpers;

use \Able\Helpers\Abstractions\AHelper;

use \Iterator;
use \Generator;
use \ArrayAccess;

class Arr extends AHelper {

	/**
	 * Determines whether the given value is presentable as an array.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public static final function castable($value): bool {
		return is_array($value)
			|| $value instanceof Iterator
			|| $value instanceof ArrayAccess

			|| (is_object($value) && method_exists($value, 'toArray'));
	}

	/**
	 * Converts the given value into an array.
	 *
	 * @attention A non-arrayable argument will be converted
	 * to a one-element array without throwing an error.
	 *
	 * @attention A null value will be converted
	 * to an empty array.
	 *
	 * @param mixed $value
	 * @return array
	 */
	public static final function cast($value): array {
		if (is_array($value)) {
			return $value;
		}

		if (is_object($value) && method_exists($value, 'toArray')) {
			return $value->toArray();
		}

		if ($value instanceof ArrayAccess) {
			return (array)$value;
		}

		if ($value instanceof Iterator) {
			return iterator_to_array($value);
		}

		if (is_null($value)) {
			return [];
		}

		return [$value];
	}

	/**
	 * Converts the given arguments into a single-level flat array.
	 *
	 * @attention Existing keys are not preserved!
	 *
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function simplify(...$args): array {
		return !empty($args)

			&& array_walk_recursive($args,
				function($v) use (&$_) { $_[] = $v; })

		? self::cast($_) : [];
	}

	/**
	 * Converts given arguments into a generator.
	 *
	 * @attention Existing keys are not preserved!
	 *
	 * @param mixed ...$arguments
	 * @return Generator
	 */
	public static final function iterate(...$arguments): Generator {
		foreach (self::simplify($arguments) as $item){
			yield $item;
		}
	}

	/**
	 * Appends the given array to the end of another one.
	 *
	 * @attention Numerical keys not preserved.
	 *
	 * @attention Duplicate keys will be removed from a source array
	 * and preserved in appended one.
	 *
	 * @param array $Source
	 * @param array $Append
	 * @return array
	 */
	public static final function append(array $Source, array $Append): array {
		return array_merge(self::only($Source, array_filter(array_keys($Source), function($value) use ($Append) {
			return is_int($value) || !array_key_exists($value, $Append); })), $Append);
	}

	/**
	 * Appends the given array to the beginning of another one.
	 *
	 * @attention Numerical keys not preserved.
	 *
	 * @attention Duplicate keys will be removed from a source array
	 * and preserved in prepended one.
	 *
	 * @param array $Source
	 * @param array $Prepend
	 * @return array
	 */
	public static final function prepend(array $Source, array $Prepend): array {
		return array_merge($Prepend, self::only($Source, array_filter(array_keys($Source), function($value) use ($Prepend){
			return is_int($value) || !array_key_exists($value, $Prepend); })));
	}

	/**
	 * Adds the given values to the end of an array.
	 * Function behavior is similar to 'array_push' but allows an arbitrary list of arguments.
	 *
	 * @attention Keys not preserved.
	 *
	 * @param array $Source
	 * @param mixed $value
	 * @return array
	 */
	public static final function push(array $Source, $value): array {
		return self::append($Source, self::simplify(array_slice(func_get_args(), 1)));
	}

	/**
	 * Adds the given values to the beginning of an array.
	 * Function behavior is similar to 'array_unshift' but allows an arbitrary list of arguments.
	 *
	 * @attention Keys not preserved.
	 *
	 * @param array $Source
	 * @param mixed $value
	 * @return array
	 */
	public static final function unshift(array $Source, $value): array {
		return self::append(self::simplify(array_slice(func_get_args(), 1)), $Source);
	}

	/**
	 * Returns a subset from the given array using the given list of needed keys.
	 *
	 * @param array $Source
	 * @param mixed $keys, ...
	 * @return array
	 */
	public static final function only(array $Source, $keys): array {
		return array_intersect_key($Source,
			array_fill_keys(self::simplify(array_slice(func_get_args(), 1)), -1));
	}

	/**
	 * Inserts an element into the specified position of an array.
	 *
	 * @param array $Source
	 * @param int $position
	 * @param $value
	 * @return array
	 */
	public static final function insert(array $Source, int $position, $value): array {
		return $position > 0 ? self::append(array_slice($Source, 0, $position), self::prepend(array_slice($Source,
			$position), self::cast($value))) : self::prepend($Source, self::cast($value));
	}

	/**
	 * Returns a subset from the given array using the given list of ignored keys.
	 *
	 * @param array $Source
	 * @param mixed $keys, ...
	 * @return array
	 */
	public static final function except(array $Source, $keys){
		return array_diff_key($Source,
			array_fill_keys(self::simplify(array_slice(func_get_args(), 1)), -1));
	}

	/**
	 * Checks if the given keys exist in an array.
	 *
	 * @param array $Source
	 * @param mixed $key, ...
	 * @return bool
	 */
	public static final function has(array $Source, $key): bool {
		return !count(array_diff(self::simplify(array_slice(func_get_args(), 1)),
			array_keys($Source)));
	}

	/**
	 * Checks if an array contains the given values.
	 *
	 * @param array $Source
	 * @param mixed $value, ...
	 * @return bool
	 */
	public static final function contains(array $Source, $value): bool {
		return count(array_intersect($Source,
			$value = self::simplify(array_slice(func_get_args(), 1)))) == count($value);
	}

	/**
	 * Returns only elements with an odd key from the given array.
	 *
	 * @attention Keys not preserved.
	 *
	 * @param array $Source
	 * @return array
	 */
	public static final function odd(array $Source){
		return array_values(array_filter(array_values($Source), function($i){
			return  ($i) % 2 == 0; }, ARRAY_FILTER_USE_KEY));
	}

	/**
	 * Returns only elements with an even key from the given array.
	 *
	 * @attention Keys not preserved.
	 *
	 * @param array $Source
	 * @return array
	 */
	public static final function even(array $Source){
		return array_values(array_filter(array_values($Source), function($i){
			return  ($i) % 2 > 0; }, ARRAY_FILTER_USE_KEY));
	}

	/**
	 * Shuffles the given array.
	 * Function behavior is similar to 'shuffle' but doesn't need sending an array by reference.
	 *
	 * @attention Keys not preserved.
	 *
	 * @param array $Source
	 * @return array
	 */
	public static final function shuffle(array $Source): array {
		return shuffle($Source) ? $Source : [];
	}

	/**
	 * Returns a random value from the given array.
	 *
	 * @param array $Source, ...
	 * @return mixed
	 */
	public static final function rand(array $Source) {
		return $Source[array_rand($Source = Arr::simplify(func_get_args()))];
	}

	/**
	 * Searches an array for a given value and returns the first corresponding key
	 * if successful or the given default value otherwise.
	 *
	 * @param array $Source
	 * @param mixed $value
	 * @param mixed $default
	 * @return mixed
	 */
	public static final function find(array $Source, $value, $default = null){
		return ($value = array_search($value, $Source, true)) !== false ? $value : $default;
	}

	/**
	 * Returns the left part of an array ending by the given value.
	 *
	 * @param array $Source
	 * @param mixed $to
	 * @return array
	 */
	public static final function left(array $Source, $to): array {
		return array_slice($Source, 0, self::find(array_keys($Source),
			$to, count($Source) - 1) + 1);
	}

	/**
	 * Returns the right part of an array starting by the given value.
	 *
	 * @param array $Source
	 * @param mixed $from
	 * @return array
	 */
	public static final function right(array $Source, $from): array {
		return array_slice($Source, self::find(array_keys($Source),
			$from, 0));
	}

	/**
	 * Creates an array by using the first given array for keys
	 * and the second one for its values.
	 *
	 * If the values array is shorter than the keys array,
	 * it expands using the given default value.
	 *
	 * @param array $Keys
	 * @param array $Values
	 * @param mixed $default
	 * @return array
	 */
	public static final function combine(array $Keys , array $Values = [], $default = null): array {
		return array_combine($Keys, array_pad(array_slice($Values, 0,
			count($Keys)), count($Keys), $default));
	}

	/**
	 * Creates an array using the even elements of given array for keys
	 * and its odd elements for values.
	 *
	 * @param mixed $Raw
	 * @return array
	 */
	public static final function compile($Raw): array {
		return self::combine(self::odd($Raw = self::simplify(func_get_args())),
			self::even($Raw));
	}

	/**
	 * Applies the given function to each element of an array
	 * or to the specified subset, if the third argument is provided.
	 *
	 * @param array $Source
	 * @param callable $Callback
	 * @param mixed $keys, ...
	 * @return array
	 */
	public static final function each(array $Source, callable $Callback, $keys = null): array {
		return array_walk($Source, function(&$value, $key, $keys) use ($Callback) {
			$value = count($keys) < 1 || in_array($key, $keys) ? $Callback($key, $value) : $value; },
				array_filter(Arr::simplify(array_slice(func_get_args(), 2)))) ? $Source : [];
	}

	/**
	 * Creates an array using the given array for keys
	 * and the given function to get values.
	 *
	 * @param array $Keys
	 * @param callable $Callback
	 * @return array
	 */
	public static final function make(array $Keys, callable $Callback = null): array {
		return Arr::combine($Keys, is_callable($Callback) ? Arr::each($Keys, function () use ($Callback){
			return call_user_func($Callback, func_get_arg(1)); }) : []);
	}

	/**
	 * Returns an array contains string elements formed by a key-value pair
	 * of the source array combined by the specified separator.
	 *
	 * @attention Keys not preserved.
	 *
	 * @attention This method throws an exception if any element
	 * of the given array cannot be represented as a string.
	 *
	 * @param array $Source
	 * @param string $separator
	 * @return array;
	 */
	public static final function pack(array $Source, string $separator): array {
		return array_walk($Source, function(&$value, $key) use ($separator){
			$value = $key . $separator . Str::cast($value); }) ? array_values($Source) : [];
	}

	/**
	 * Converts an array packed by the previous function into its original state.
	 * @see Arr::pack()
	 *
	 * @attention Keys not preserved.
	 *
	 * @attention This method throws an exception if any element
	 * of the given array cannot be represented as a string.
	 *
	 * @param array $Source
	 * @param string $separator
	 * @return array
	 */
	public static final function unpack(array $Source, string $separator): array {
		return array_walk($Source, function(&$value, $key) use ($separator) { $value = array_map('trim',
			array_pad(preg_split('/(?:' . preg_quote($separator, '/') . ')/', Str::cast($value), 2), 2, null)); })
				? self::compile(self::simplify($Source)) : [];
	}

	/**
	 * Tries to retrieve a single value from an array by its key
	 * or returns the default value if the key does not exist.
	 *
	 * @attention If the needed value exists but equals to null,
	 * the default value will be returned!
	 *
	 * @param array $Source
	 * @param mixed $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static final function get(array $Source, $key, $default = null) {
		return array_key_exists($key, $Source)
			&& !is_null($Source[$key]) ? $Source[$key] : $default;
	}

	/**
	 * Tries to retrieve a single value from an array by its position
	 * or returns the default value if the position is out of range.
	 *
	 * @attention If the needed value exists but equals to null,
	 * the default value will be returned!
	 *
	 * @attention The numbering starts from zero.
	 *
	 * @param array $Source
	 * @param int $index
	 * @param mixed $default
	 * @return mixed
	 */
	public static final function value(array $Source, int $index = 0, $default = null){
		return self::get(array_values($Source), $index, $default);
	}

	/**
	 * Returns the first element of an array or null if the array is empty.
	 *
	 * @param array $Source
	 * @param mixed $default
	 * @return mixed
	 */
	public static final function first(array $Source, $default = null) {
		return self::value($Source, 0, $default);
	}

	/**
	 * Returns the last element of an array or null if the array is empty.
	 *
	 * @param array $Source
	 * @param mixed $default
	 * @return mixed
	 */
	public static final function last(array $Source, $default = null){
		return self::value($Source, count($Source) - 1, $default);
	}

	/**
	 * Tries to retrieve a single key from an array by its position
	 * or returns the default value if the position is out of range.
	 *
	 * @attention If the needed key exists but equals to null,
	 * the default value will be returned!
	 *
	 * @attention The numbering starts from zero.
	 *
	 * @param array $Source
	 * @param int $index
	 * @param mixed $default
	 * @return mixed
	 */
	public static final function key(array $Source, int $index = 0, $default = null){
		return self::get(array_keys($Source), $index, $default);
	}

	/**
	 * Tries to retrieve a single value or a subset
	 * from an array by the given sequence of keys.
	 *
	 * @param array $Source
	 * @param mixed $keys, ...
	 * @return mixed
	 */
	public static final function path(array $Source, $keys){
		return count($keys = array_filter(self::simplify(array_slice(func_get_args(), 1)))) > 0 ? (isset($Source[$keys[0]])
			? (count($keys) > 1 ? (is_array($Source[$keys[0]]) ? self::path($Source[$keys[0]], array_slice($keys, 1)) : null)
				: $Source[$keys[0]]) : null) : $Source;
	}

	/**
	 * Adds the last given argument into an array following the specified path
	 * defined by the sequence from the second argument to the penultimate one.
	 *
	 * @param array $Source
	 * @param mixed $keys, ...
	 * @param mixed $value
	 * @return array
	 */
	public static final function improve(array $Source, $keys, $value): array {
		return count($keys = array_filter(self::simplify(array_slice(func_get_args(), 1, func_num_args() - 2)))) > 0
			? (array_merge($Source, [$keys[0] => self::improve(self::cast(self::get($Source, $keys[0], [])), array_slice($keys, 1),
				Arr::last(func_get_args()))])): self::push($Source, $value);
	}

	/**
	 * Removes an element from an array by the path defined
	 * by the sequence from the second argument to last one.
	 *
	 * @param array $Source
	 * @param mixed $keys, ...
	 * @return array
	 */
	public static final function clear(array $Source, $keys): array {
		return count($keys = self::simplify(array_slice(func_get_args(), 1))) > 0 ? (count($keys) > 1
			? array_merge($Source, [$keys[0] => self::clear(self::cast(self::get($Source, $keys[0], [])),
				array_slice($keys, 1))]) : self::except($Source, $keys[0])): $Source;
	}

	/**
	 * Filters an array, keeping only selected values.
	 *
	 * @param array $Source
	 * @param $values
	 * @return array
	 */
	public static final function select(array $Source, $values): array {
		return array_intersect($Source, self::simplify(array_slice(func_get_args(), 1)));
	}

	/**
	 * @param array $Source
	 * @param int $length
	 * @param null $default
	 * @return array
	 */
	public static final function take(array $Source, int $length, $default = null): array {
		return array_pad(array_slice($Source, 0, $length), $length, $default);
	}

	/**
	 * Sorts an array keys in the order given by arguments.
	 *
	 * @attention Missing keys will be removed.
	 *
	 * @param array $Source
	 * @param mixed $keys, ...
	 * @return array
	 */
	public static final function like(array $Source, $keys){
		return count($keys = self::simplify(array_slice(func_get_args(), 1))) && uksort($Source, function($l, $r) use ($keys){
			return Arr::contains($keys, $r, $l) ? array_search($l, $keys) - array_search($r, $keys) : 0; })
				? self::only($Source, $keys) : $Source;
	}

	/**
	 * Sorts an array by a custom function.
	 *
	 * @param array $Source
	 * @param callable $Handler
	 * @return array
	 */
	public static final function sort(array $Source, callable $Handler): array {
		return usort($Source, $Handler) ? $Source : [];
	}
}
