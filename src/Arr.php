<?php
namespace Able\Helpers;

use \Able\Helpers\Abstractions\AHelper;

use \Iterator;
use \Generator;
use \ArrayAccess;

class Arr extends AHelper {

	/**
	 * Determines whether the given value
	 * is presentable as an array.
	 *
	 * @param mixed $value
	 * @return bool
	 */
	public static final function castable(mixed $value): bool {
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
	public static final function cast(mixed $value): array {
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
	 * Merges the mixed-types list of arguments into an array.
	 *
	 * @attention Numerical keys not preserved.
	 *
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function collect(mixed ...$args): array {
		return array_merge(...array_map(function ($_) {
			return self::cast($_); }, $args));
	}

	/**
	 * Creates a new array using the
	 * first array for keys and the second one for values.
	 *
	 * @attention If the second array is shorter than the first one,
	 * it will be expanded and filled by the default value.
	 *
	 * @attention If the second array is longer than the first one,
	 * the extra elements will be ignored.
	 *
	 * @param array $Keys
	 * @param array $Values
	 * @param mixed $default
	 * @return array
	 */
	public static final function combine(array $Keys , array $Values = [], mixed $default = null): array {
		return array_combine($Keys,

			array_pad(array_slice($Values, 0,
				count($Keys)), count($Keys), $default));
	}

	/**
	 * Creates an array using the even elements for keys
	 * and odd elements for values.
	 *
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function compile(mixed ...$args): array {
		$args = self::simplify($args);

		return self::combine(
			self::odd($args), self::even($args));
	}

	/**
	 * Creates a new array using the first argument for keys
	 * and the callable handler to get values.
	 *
	 * @param array $Keys
	 * @param callable|null $Handler
	 * @return array
	 */
	public static final function make(array $Keys, ?callable $Handler = null): array {
		return Arr::combine($Keys,

		is_callable($Handler) ? Arr::each($Keys, function () use ($Handler){
			return call_user_func($Handler, func_get_arg(1)); }) : []);
	}

	/**
	 * Converts the given arguments into a single-level flat array.
	 *
	 * @attention Existing keys are not preserved!
	 *
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function simplify(mixed ...$args): array {
		return !empty($args)

			&& array_walk_recursive($args, function($v) use (&$_) {
				if (!is_null($v)) { $_[] = $v; } })

		? self::cast($_) : [];
	}

	/**
	 * Converts the given arguments into a generator.
	 *
	 * @attention Existing keys are not preserved!
	 *
	 * @param mixed ...$arguments
	 * @return Generator
	 */
	public static final function iterate(mixed ...$arguments): Generator {
		foreach (self::simplify($arguments) as $item){
			yield $item;
		}
	}

	/**
	 * Converts a given array into a generator and erases all its elements.
	 *
	 * @param array $Source
	 * @return Generator
	 */
	public static final function degrade(array &$Source): Generator {
		while(count($Source) > 0) {
			yield array_shift($Source);
		}
	}

	/**
	 * Appends the array given as a first argument
	 * to the end of another array given as a second argument.
	 *
	 * @attention Numerical keys not preserved.
	 *
	 * @attention Duplicate keys, if any, will keep the values
	 * from the first array.
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
	 * Prepends the array given as a first argument
	 * to the beginning of another array given as a second argument.
	 *
	 * @attention Numerical keys not preserved.
	 *
	 * @attention Duplicate keys, if any, will keep the values
	 * from the first array.
	 *
	 * @param array $Source
	 * @param array $Prepend
	 * @return array
	 */
	public static final function prepend(array $Source, array $Prepend): array {
		return array_merge($Prepend, self::only($Source, array_filter(array_keys($Source), function($value) use ($Prepend) {
			return is_int($value) || !array_key_exists($value, $Prepend); })));
	}

	/**
	 * Adds given values to the end of an array.
	 *
	 * Function behavior is similar to 'array_push'
	 * but takes a custom list of mixed-type arguments.
	 *
	 * @param array $Source
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function push(array $Source, mixed ...$args): array {
		return self::append($Source, self::cast($args));
	}

	/**
	 * Removes and returns the ending value from an array.
	 *
	 * Function behavior is similar to 'array_push'
	 * but takes a custom list of mixed-type arguments.
	 *
	 * @param array $Source
	 * @return mixed
	 */
	public static final function pop(array &$Source): mixed {
		return array_pop($Source);
	}

	/**
	 * Adds given values to the beginning of an array.
	 *
	 * Function behavior is similar to 'array_unshift'
	 * but takes a custom list of mixed-type arguments.
	 *
	 * @param array $Source
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function unshift(array $Source, mixed ...$args): array {
		return self::prepend($Source, self::cast(array_reverse($args)));
	}

	/**
	 * Removes and returns leading values of an array.
	 *
	 * Function behavior is similar to 'array_unshift'
	 * but takes a custom list of mixed-type arguments.
	 *
	 * @param array $Source
	 * @return mixed
	 */
	public static final function shift(array &$Source): mixed {
		return array_shift($Source);
	}

	/**
	 * Inserts an element into the specified position of the given array.
	 *
	 * @param array $Source
	 * @param int $position
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function insert(array $Source, int $position, mixed ...$args): array {
		return $position > 0

			? array_merge(
				array_slice($Source, 0, $position),
				self::collect(...$args),
				array_slice($Source, $position)
			)

		: self::prepend($Source, self::collect(...$args));
	}

	/**
	 * Adds the last argument into an array given as a first argument,
	 * following the path specified by the sequence between
	 * the first argument and the last one.
	 *
	 * @attention The only integer numbers
	 * and strings allowed as keys - something other will be ignored.
	 * I can cause unexpected behavior.
	 *
	 * @param array $Source
	 * @param mixed ...$args
	 * @return void
	 */
	public static final function improve(array &$Source, mixed ...$args): void {
		$_ = array_shift($args);

		if (count($args) > 0) {
			$Source[$_] = self::cast($Source[$_] ?? []);

			if (!is_null($_)) {
				self::improve($Source[$_], ...$args);
			}
		} else {
			$Source = array_merge($Source, self::cast($_));
		}
	}

	/**
	 * Merges two given arrays recursively.
	 *
	 * @attention Numerical keys not preserved.
	 *
	 * @param array $Source
	 * @param array $Supplier
	 * @return array
	 */
	public static final function merge(array $Source, array $Supplier): array {
		return array_walk($Supplier,
			function($value, $key) use (&$Source) {

				if (is_array($value)) {
					$Source[$key] = self::merge(self::cast(self::get($Source, $key)), $value);

				} elseif (is_numeric($key)) {
					$Source[] = $value;

				} elseif (array_key_exists($key, $Source)) {
					self::improve($Source, $key, $value);

				} else {
					$Source[$key] = $value;
				}

			}) ? $Source

		: [];
	}

	/**
	 * Unite two given arrays recursively.
	 *
	 * @param array $Source
	 * @param array $Supplier
	 * @return array
	 */
	public static final function unite(array $Source, array $Supplier): array {
		return array_walk($Supplier,
			function($value, $key) use (&$Source) {

				if (is_array($value)) {
					$Source[$key] = self::unite(self::cast(self::get($Source, $key)), $value);
				} else {
					$Source[$key] = $value;
				}

			}) ? $Source

		: [];
	}

	/**
	 * Removes an element from the array given as a first argument following the path
	 * defined by the sequence starting the second argument to last one.
	 *
	 * @attention The only integer numbers
	 * and strings allowed as keys - something other will be ignored.
	 * I can cause unexpected behavior.
	 *
	 * @param array $Source
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function erase(array $Source, mixed ...$args): array {
		return count($keys = array_filter($args,

			function ($_) {
				return is_integer($_) || is_string($_); })) > 0

					&& array_key_exists($keys[0], $Source)

			? (count($keys) > 1

				? array_merge($Source, [$keys[0] => self::erase(self::cast(self::get($Source, $keys[0])),
					...array_slice($args, 1))])

			: self::except($Source, $keys[0]))

		: $Source;
	}

	/**
	 * Returns a single value from an array by its key
	 * or the default value if the given key does not exist.
	 *
	 * @attention The default value will be returned instead of null.
	 *
	 * @param array $Source
	 * @param string|int|null $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static final function get(array $Source, string|int|null $key, mixed $default = null): mixed {
		return array_key_exists($key, $Source)
			&& !is_null($Source[$key]) ? $Source[$key] : $default;
	}

	/**
	 * Returns a single value from the given array by its position
	 * or the default value if the position is out of range.
	 *
	 * @attention The default value will be returned instead of null.
	 *
	 * @param array $Source
	 * @param int $position
	 * @param mixed $default
	 * @return mixed
	 */
	public static final function value(array $Source, int $position = 0, mixed $default = null): mixed {
		return self::get(array_values($Source), $position, $default);
	}

	/**
	 * Returns a single key of the given array by its position
	 * or the default value if the position is out of range.
	 *
	 * @attention The default value will be returned instead of null.
	 *
	 * @param array $Source
	 * @param int $position
	 * @param mixed $default
	 * @return string|int|null
	 */
	public static final function key(array $Source, int $position = 0, mixed $default = null): string|int|null {
		return self::get(array_keys($Source), $position, $default);
	}

	/**
	 * Searches an array for a given value and returns
	 * the first corresponding key or the default value if nothing found.
	 *
	 * @param array $Source
	 * @param mixed $value
	 * @param mixed $default
	 * @return mixed
	 */
	public static final function find(array $Source, mixed $value, mixed $default = null): mixed {
		return ($key = array_search($value, $Source, true)) !== false ? $key : $default;
	}

	/**
	 * Tries to retrieve a single value or a subset
	 * from an array by the given sequence of keys.
	 *
	 * @attention The only string keys are acceptable!
	 *
	 * @param array $Source
	 * @param mixed ...$args
	 * @return mixed
	 */
	public static final function follow(array $Source, string ...$args): mixed {
		return count($args) > 1 ?

			self::follow(self::cast(Arr::get($Source, $args[0])),
				...array_slice($args, 1))

		: Arr::get($Source, Arr::first($args));
	}

	/**
	 * Tries to apply the given function to a single value or a subset
	 * extracted from an array by the given sequence of keys.
	 *
	 * @attention The only string keys are acceptable!
	 *
	 * @param array $Source
	 * @param callable $Handler
	 * @param string ...$args
	 * @return mixed
	 */
	public static final function apply(array $Source, callable $Handler, string ...$args): array {
		if (count($args) > 1
			&& is_array($Source[$args[0]])) {

				$Source[$args[0]] = self::apply($Source[$args[0]], $Handler, ...array_slice($args, 1));

		} elseif (count($args) > 0
			&& array_key_exists($args[0], $Source)) {

				$Source[$args[0]] = call_user_func($Handler, $Source[$args[0]]);
		}

		return $Source;
	}

	/**
	 * @param array $Source
	 * @param mixed $value
	 * @param string ...$args
	 * @return array
	 */
	public static final function place(array $Source, mixed $value, string ...$args): array {
		if (count($args) > 1) {
			$Source[$args[0]] = self::place(Arr::cast(Arr::get($Source, $args[0])),
				$value, ...array_slice($args, 1));

		}  elseif (count($args) > 0) {
			$Source[$args[0]] = $value;
		}

		return $Source;
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
	 * @param array $Source
	 * @param int $length
	 * @return array
	 */
	public static final function cut(array $Source, int $length = 1): array {
		return array_slice($Source, 0, $length > 0 && count($Source) > $length ? count($Source) - $length : 0);
	}

	/**
	 * Returns a random value from the array.
	 *
	 * @param array $Source
	 * @return mixed
	 */
	public static final function rand(array $Source): mixed {
		return $Source[array_rand($Source)];
	}

	/**
	 * Returns a subset containing the given keys only.
	 *
	 * @param array $Source
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function only(array $Source, mixed ...$args): array {
		return array_intersect_key($Source,
			array_fill_keys(self::simplify($args), -1));
	}

	/**
	 * Returns a subset containing all array values except the given keys.
	 *
	 * @param array $Source
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function except(array $Source, mixed ...$args): array {
		return array_diff_key($Source,
			array_fill_keys(self::simplify(...$args), -1));
	}

	/**
	 * Returns the first element of the array.
	 *
	 * @param array $Source
	 * @param mixed $default
	 * @return mixed
	 */
	public static final function first(array $Source, mixed $default = null): mixed {
		return self::value($Source, 0, $default);
	}

	/**
	 * Returns the last element of the array.
	 *
	 * @param array $Source
	 * @param mixed $default
	 * @return mixed
	 */
	public static final function last(array $Source, mixed $default = null): mixed {
		return self::value($Source, count($Source) - 1, $default);
	}

	/**
	 * Returns only odd elements from the given array.
	 *
	 * @attention Keys not preserved.
	 *
	 * @param array $Source
	 * @return array
	 */
	public static final function odd(array $Source): array {
		return array_values(array_filter(array_values($Source), function($i) {
			return  ($i) % 2 == 0; }, ARRAY_FILTER_USE_KEY));
	}

	/**
	 * Returns only even elements from the given array.
	 *
	 * @attention Keys not preserved.
	 *
	 * @param array $Source
	 * @return array
	 */
	public static final function even(array $Source): array {
		return array_values(array_filter(array_values($Source), function($i) {
			return  ($i) % 2 > 0; }, ARRAY_FILTER_USE_KEY));
	}

	/**
	 * Returns the left part of the array ending by the given value
	 *
	 * @param array $Source
	 * @param string|int $to
	 * @return array
	 */
	public static final function left(array $Source, string|int $to): array {
		return array_slice($Source, 0,
			self::find(array_keys($Source), $to, count($Source) - 1) + 1);
	}

	/**
	 * Returns the right part of the array starting from the given value
	 *
	 * @param array $Source
	 * @param string|int $from
	 * @return array
	 */
	public static final function right(array $Source, string|int $from): array {
		return array_slice($Source,
			self::find(array_keys($Source), $from, 0));
	}

	/**
	 * Filters an array, keeping only selected values if presented.
	 *
	 * @param array $Source
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function select(array $Source, mixed ...$args): array {
		return array_intersect($Source, self::simplify($args));
	}

	/**
	 * Checks if the given keys present in the array.
	 *
	 * @param array $Source
	 * @param string|int|array ...$args
	 * @return bool
	 */
	public static final function has(array $Source, string|int|array ...$args): bool {
		return !count(array_diff(self::simplify(...$args), array_keys($Source)));
	}

	/**
	 * Checks if the array contains the given values.
	 *
	 * @param array $Source
	 * @param mixed ...$args
	 * @return bool
	 */
	public static final function contains(array $Source, mixed ...$args): bool {
		return count(array_intersect($Source, $args = self::simplify($args))) == count($args);
	}

	/**
	 * Shuffles the array.
	 * This function behavior is similar to 'shuffle' but doesn't need send an array by reference.
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
	 * Sorts the array by values using the handler.
	 *
	 * @attention Keys not preserved.
	 *
	 * @param array $Source
	 * @param callable|null $Handler
	 * @return array
	 */
	public static final function sort(array $Source, ?callable $Handler = null): array {
		return call_user_func(function() use (&$Source, $Handler) {

			return !is_null($Handler)
				? usort($Source, $Handler) : sort($Source);

		}) ? $Source : [];
	}

	/**
	 * Sorts the array by keys using the handler.
	 *
	 * @param array $Source
	 * @param callable|null $Handler
	 * @return array
	 */
	public static final function ksort(array $Source, ?callable $Handler = null): array {
		return call_user_func(function() use (&$Source, $Handler) {
			return !is_null($Handler)
				? uksort($Source, $Handler) : ksort($Source);

		}) ? $Source : [];
	}

	/**
	 * Sorts the array by keys following the order determined by arguments.
	 *
	 * @attention Extra keys will be removed.
	 *
	 * @param array $Source
	 * @param string|int|array ...$args
	 * @return array
	 */
	public static final function like(array $Source, string|int|array ...$args): array {
		return count($keys = self::simplify($args)) > 0

			? self::ksort(self::only($Source, ...$keys),

				function($a, $b) use ($keys) {
					return array_search($a, $keys) - array_search($b, $keys); })

		: [];
	}

	/**
	 * Applies the handler to each element of the array
	 * or the specified subset in case the third argument provided.
	 *
	 * @param array $Source
	 * @param callable $Handler
	 * @param mixed ...$args
	 * @return array
	 */
	public static final function each(array $Source, callable $Handler, mixed ...$args): array {
		return array_walk($Source,
			function(&$value, $key, $args) use ($Handler) {

				$value = empty($args) || in_array($key, $args)
					? $Handler($key, $value) : $value; },

			array_filter($args, function($_){
				return is_integer($_) || is_string($_); })) ? $Source : [];
	}

	/**
	 * Creates a new array of using key-value pair
	 * of the provided array separated by the specified delimiter.
	 *
	 * @attention Keys not preserved.
	 *
	 * @attention Throws an exception if any element
	 * of the given array cannot be represented as a string.
	 *
	 * @param array $Source
	 * @param string $delimiter
	 * @return array;
	 */
	public static final function pack(array $Source, string $delimiter): array {
		$_ = '/' . preg_quote($delimiter, '/') . '.*$/';

		return array_walk($Source,
			function(&$value, $key) use ($delimiter, $_) {

				$value = Str::join($delimiter,
					preg_replace($_, '', $key),  Str::cast($value)); })

		? array_values($Source) : [];
	}

	/**
	 * Converts a packed array into the original state.
	 * @see Arr::pack()
	 *
	 * @attention Keys not preserved.
	 *
	 * @attention Throws an exception if any element
	 * of the given array cannot be represented as a string.
	 *
	 * @param array $Source
	 * @param string $delimiter
	 * @return array
	 */
	public static final function unpack(array $Source, string $delimiter): array {
		$_ = '/(?:' . preg_quote($delimiter, '/') . ')/';

		return array_walk($Source,
			function(&$value) use ($_) {

			$value = array_map('trim',
				array_pad(preg_split($_, Str::cast($value), 2), 2, '')); })

		? self::compile($Source) : [];
	}
}
