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

namespace Haeckel\Generics;

use Haeckel\Exc;
use Haeckel\Exc\Util\MsgProvider;
use Haeckel\Generics\Filter\ValueFilter;
use Haeckel\Generics\Type;

/**
 * @template TValue
 * @implements Collection<TValue>
 */
abstract class BaseCollection implements Collection
{
    /** @var array<int,TValue> */
    private array $collection;
    private Type\Definition $type;

    /**
     * @param TValue $elements
     *
     * @throws \InvalidArgumentException
     *
     * @no-named-arguments
     */
    final public function __construct(mixed ...$elements)
    {
        $this->type = static::getElementType();
        foreach ($elements as $key => $elem) {
            if (! $this->type->isOfType($elem)) {
                throw new \InvalidArgumentException(
                    MsgProvider::createTypeErrMsg(
                        ++$key,
                        $this->type->getTypeName(),
                        \get_debug_type($elem),
                    ),
                );
            }
        }
        $this->collection = $elements;
    }

    abstract public static function getElementType(): Type\Definition;

    // #region Iterator
    /**
     * @return TValue
     *
     * @throws \OutOfRangeException
     */
    public function current(): mixed
    {
        $val = \current($this->collection);
        if ($val === false) {
            $msg = (
                $this->isEmpty()
                ? 'called current on empty collection'
                : 'pointer moved beyond end of elements'
            );
            throw new \OutOfRangeException($msg);
        }

        return $val;
    }

    public function next(): void
    {
        \next($this->collection);
    }

    /** @throws \OutOfRangeException */
    public function key(): int
    {
        $key = \key($this->collection);
        if ($key === null) {
            throw new \OutOfRangeException();
        }

        return $key;
    }

    public function valid(): bool
    {
        return \key($this->collection) !== null;
    }

    public function rewind(): void
    {
        \reset($this->collection);
    }
    // #endregion

    public function count(): int
    {
        return \count($this->collection);
    }

    /** @return array<int,TValue> */
    public function jsonSerialize(): array
    {
        return $this->collection;
    }

    public function clear(): void
    {
        $this->collection = [];
        \reset($this->collection);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /** @return array<int,TValue> */
    public function toArray(): array
    {
        return $this->collection;
    }

    /** @param TValue $elements */
    public function add(mixed ...$elements): void
    {
        foreach ($elements as $key => $element) {
            if (! $this->type->isOfType($element)) {
                throw new \InvalidArgumentException(
                    Exc\Util\MsgProvider::createTypeErrMsg(
                        ++$key,
                        $this->type->getTypeName(),
                        \get_debug_type($element),
                    )
                );
            }
            $this->collection[] = $element;
        }
    }

    /**
     * @no-named-arguments
     * @param TValue $elements
     */
    public function remove(mixed ...$elements): void
    {
        foreach ($elements as $key => $elem) {
            if (! $this->type->isOfType($elem)) {
                throw new \InvalidArgumentException(
                    Exc\Util\MsgProvider::createTypeErrMsg(
                        ++$key,
                        $this->type->getTypeName(),
                        \get_debug_type($elem),
                    )
                );
            }

            $strict = ! \is_object($elem);
            foreach ($this->collection as $collectionKey => $collectionElem) {
                /**
                 * @unsafe
                 * @phpstan-ignore equal.notAllowed (necessary for object cmp)
                 */
                $found = $strict ? $elem === $collectionElem : $elem == $collectionElem;
                if ($found) {
                    unset($this->collection[$collectionKey]);
                }
            }
        }
    }

    /**
     * find elements matching a filter
     *
     * @param ValueFilter<TValue> $filter
     * @return static<TValue>
     */
    public function find(ValueFilter $filter): static
    {
        return new static(...\array_filter($this->collection, $filter));
    }

    /**
     * @param ValueFilter<TValue> $filter
     *
     * @return TValue|null
     */
    public function findFirst(ValueFilter $filter): mixed
    {
        foreach ($this->collection as $elem) {
            if ($filter($elem)) {
                return $elem;
            }
        }

        return null;
    }

    /** @param ValueFilter<TValue> $filter */
    public function removeIf(ValueFilter $filter): void
    {
        foreach ($this->collection as $key => $element) {
            if ($filter($element)) {
                unset($this->collection[$key]);
            }
        }
    }
}
