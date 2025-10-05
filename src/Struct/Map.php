<?php

/**
 * @copyright 2025 Dominik Häckel
 * @license LGPL-3.0-or-later
 *
 * This file is part of haeckel/php-generics.
 *
 * haeckel/php-generics is free software:
 * you can redistribute it and/or modify it under the terms of the GNU Lesser General Public License
 * as published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * haeckel/php-generics is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * haeckel/php-generics.
 * If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Haeckel\Generics\Struct;

use Haeckel\Generics\Struct\HashTable\Entry;
use Haeckel\Generics\Hashable;

/**
 * @template TKey of string|\Stringable|Hashable
 * @template TValue
 * @extends \Iterator<TKey,TValue>
 */
interface Map extends \Countable, \Iterator, \JsonSerializable
{
    /** remove all entries */
    public function clear(): void;

    /**
     * @param TValue $value
     *
     * @throws \InvalidArgumentException
     */
    public function contains(mixed $value): bool;

    /**
     * @param TKey $key
     *
     * @throws \InvalidArgumentException
     */
    public function containsKey(string|\Stringable|Hashable $key): bool;

    /** @return array<string,TValue> */
    public function toArray(): array;

    /** @return array<string,Entry<TKey,TValue>> */
    public function toArrayPreserveEntries(): array;

    /**
     * @return TValue|null
     *
     * @throws \InvalidArgumentException
     */
    public function get(string|\Stringable|Hashable $key): mixed;

    public function isEmpty(): bool;

    /**
     * must return the keys as given, so e.g. an array of objects implementing Hashable|\Stringable
     * instead of only the string representation
     *
     * @return array<TKey>
     */
    public function getKeys(): array;

    /**
     * creates new entry with if key does not exist, replace value if key exists already
     *
     * @param TKey $key
     * @param TValue $value
     *
     * @throws \InvalidArgumentException
     */
    public function put(string|\Stringable|Hashable $key, mixed $value): void;

    /**
     * Add new entry if key is not already present, else do nothing and return curr val associated
     * with given key.
     *
     * @param TKey $key
     * @param TValue $value
     *
     * @return TValue|null if succeeded return null, else return curr value
     *
     * @throws \InvalidArgumentException
     */
    public function putIfAbsent(string|\Stringable|Hashable $key, mixed $value): mixed;

    /**
     * @return TValue|null curr value if key was found and removed, null otherwise
     *
     * @throws \InvalidArgumentException
     */
    public function remove(string|\Stringable|Hashable $key): mixed;

    /**
     * Remove entry if given key is mapped to given value
     *
     * @param TKey $key
     * @param TValue $value
     *
     * @throws \InvalidArgumentException
     */
    public function removeIfMappedToKey(string|\Stringable|Hashable $key, mixed $value): bool;

    /**
     * Replace value of given key, does nothing if key does not exist
     * @param TKey $key
     * @param TValue $value
     *
     * @return TValue|null return curr value when replaced, null otherwise (when key does not exist)
     *
     * @throws \InvalidArgumentException
     */
    public function replace(string|\Stringable|Hashable $key, mixed $value): mixed;

    /**
     * Replace with given newVal when given key is currently mapped to old value
     * @param TKey $key
     * @param TValue $oldVal
     * @param TValue $newVal
     *
     * @return bool if op succeeded
     *
     * @throws \InvalidArgumentException
     */
    public function replaceIfOldValMatches(
        string|\Stringable|Hashable $key,
        mixed $oldVal,
        mixed $newVal,
    ): bool;

    /** @return array<TValue> values dissociated from keys */
    public function getValues(): array;
}
