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

namespace Haeckel\Generics\Struct\HashTable;

use Haeckel\Exc\Util\MsgProvider;
use Haeckel\Generics\{
    Cmp,
    Hashable,
    Struct\HashTable,
    Type,
};

/**
 * @template TKey of string|\Stringable|Hashable
 * @template TValue
 * @implements HashTable<TKey,TValue>
 */
abstract class Base implements HashTable
{
    /** @var array<string,Entry<TKey,TValue>> */
    private array $hashTable;
    private Type\Definition $keyType;
    private Type\Definition $valueType;

    final public function __construct()
    {
        $this->keyType = static::getKeyType();
        $this->valueType = static::getValueType();
        $this->hashTable = [];
    }

    /** may only be types that pass the Union Type string|\Stringable|Hashable */
    abstract public static function getKeyType(): Type\Definition;

    abstract public static function getValueType(): Type\Definition;

    public function clear(): void
    {
        $this->hashTable = [];
    }

    public function contains(mixed $value): bool
    {
        $this->guardAgainstInvalidValue($value, 1);

        foreach ($this->hashTable as $entry) {
            if (Cmp::areEqual($value, $entry->value)) {
                return true;
            }
        }

        return false;
    }

    public function containsKey(string|\Stringable|Hashable $key): bool
    {
        $this->guardAgainstInvalidKey($key, 1);
        $lookupKey = $this->keyToString($key);

        return \array_key_exists($lookupKey, $this->hashTable);
    }

    public function toArray(): array
    {
        $flattened = [];
        foreach ($this->hashTable as $key => $entry) {
            $flattened[$key] = $entry->value;
        }

        return $flattened;
    }

    public function toArrayPreserveEntries(): array
    {
        return $this->hashTable;
    }

    public function get(string|\Stringable|Hashable $key): mixed
    {
        $this->guardAgainstInvalidKey($key, 1);

        $lookupKey = $this->keyToString($key);
        $entry = $this->hashTable[$lookupKey] ?? null;
        return $entry?->value;
    }

    public function isEmpty(): bool
    {
        return $this->hashTable === [];
    }

    public function getKeys(): array
    {
        $keyArr = [];
        foreach ($this->hashTable as $entry) {
            $keyArr[] = $entry->key;
        }

        return $keyArr;
    }

    public function put(string|\Stringable|Hashable $key, mixed $value): void
    {
        $this->guardAgainstInvalidKey($key, 1);
        $this->guardAgainstInvalidValue($value, 2);

        $stringKey = $this->keyToString($key);
        $this->hashTable[$stringKey] = new Entry($key, $value);
    }

    public function putIfAbsent(string|\Stringable|Hashable $key, mixed $value): mixed
    {
        $this->guardAgainstInvalidKey($key, 1);
        $this->guardAgainstInvalidValue($value, 2);

        $stringKey = $this->keyToString($key);
        $currVal = $this->hashTable[$stringKey] ?? null;
        if ($currVal !== null) {
            return $currVal->value;
        }

        $this->hashTable[$stringKey] = new Entry($key, $value);
        return null;
    }

    public function remove(string|\Stringable|Hashable $key): mixed
    {
        $this->guardAgainstInvalidKey($key, 1);

        $stringKey = $this->keyToString($key);
        $currVal = $this->hashTable[$stringKey] ?? null;
        unset($this->hashTable[$stringKey]);

        return $currVal?->value;
    }

    public function removeIfMappedToKey(string|\Stringable|Hashable $key, mixed $value): bool
    {
        $this->guardAgainstInvalidKey($key, 1);
        $this->guardAgainstInvalidValue($value, 2);

        $stringKey = $this->keyToString($key);
        $currEntry = $this->hashTable[$stringKey] ?? null;
        $currValue = $currEntry?->value;
        if ($currValue === null) {
            return false;
        }

        if (Cmp::areEqual($value, $currValue)) {
            unset($this->hashTable[$stringKey]);
            return true;
        }

        return false;
    }

    public function replace(string|\Stringable|Hashable $key, mixed $value): mixed
    {
        $this->guardAgainstInvalidKey($key, 1);
        $this->guardAgainstInvalidValue($value, 2);

        $stringKey = $this->keyToString($key);
        $currEntry = $this->hashTable[$stringKey] ?? null;
        if ($currEntry !== null) {
            $this->hashTable[$stringKey] = new Entry($key, $value);
            return $currEntry->value;
        }

        return null;
    }

    public function replaceIfOldValMatches(
        string|\Stringable|Hashable $key,
        mixed $oldVal,
        mixed $newVal,
    ): bool {
        $this->guardAgainstInvalidKey($key, 1);
        $this->guardAgainstInvalidValue($oldVal, 2, '$oldVal');
        $this->guardAgainstInvalidValue($newVal, 3, '$newVal');

        $stringKey = $this->keyToString($key);
        $currEntry = $this->hashTable[$stringKey] ?? null;
        if (! Cmp::areEqual($oldVal, $currEntry?->value)) {
            return false;
        }

        $this->hashTable[$stringKey] = new Entry($key, $newVal);
        return true;
    }

    public function getValues(): array
    {
        $vals = [];
        foreach ($this->hashTable as $entry) {
            $vals[] = $entry->value;
        }

        return $vals;
    }

    public function count(): int
    {
        return \count($this->hashTable);
    }

    /**
     * @return TValue
     *
     * @throws \OutOfRangeException
     */
    public function current(): mixed
    {
        $entry = \current($this->hashTable);
        if ($entry === false) {
            $msg = (
                $this->isEmpty()
                ? 'called current on empty hashTable'
                : 'pointer moved beyond end of elements'
            );
            throw new \OutOfRangeException($msg);
        }

        return $entry->value;
    }

    public function next(): void
    {
        \next($this->hashTable);
    }

    /**
     * @return TKey
     *
     * @throws \OutOfRangeException
     */
    public function key(): mixed
    {
        $entry = \current($this->hashTable);
        if ($entry === false) {
            $msg = (
                $this->isEmpty()
                ? 'called key on empty hashTable'
                : 'pointer moved beyond end of elements'
            );
            throw new \OutOfRangeException($msg);
        }

        return $entry->key;
    }

    public function valid(): bool
    {
        return \key($this->hashTable) !== null;
    }

    public function rewind(): void
    {
        \reset($this->hashTable);
    }

    /** @return array<string,TValue> */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    private function keyToString(string|\Stringable|Hashable $key): string
    {
        return match (true) {
            $key instanceof Hashable => $key->getHash(),
            $key instanceof \Stringable => (string) $key,
            default => $key,
        };
    }

    /** @throws \InvalidArgumentException */
    private function guardAgainstInvalidValue(
        mixed $value,
        int $paramPos,
        string $paramName = '$value',
    ): void {
        if (! $this->valueType->isOfType($value)) {
            throw new \InvalidArgumentException(
                MsgProvider::createTypeErrMsg(
                    $paramPos,
                    $this->valueType->getTypeName(),
                    \get_debug_type($value),
                    $paramName,
                ),
            );
        }
    }

    /** @throws \InvalidArgumentException */
    private function guardAgainstInvalidKey(
        mixed $key,
        int $paramPos,
        string $paramName = '$key',
    ): void {
        if (! $this->keyType->isOfType($key)) {
            throw new \InvalidArgumentException(
                MsgProvider::createTypeErrMsg(
                    $paramPos,
                    $this->keyType->getTypeName(),
                    \get_debug_type($key),
                    $paramName,
                ),
            );
        }
    }
}
