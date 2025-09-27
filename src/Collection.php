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

/**
 * @template V
 * @implements CollectionIface<V>
 */
abstract class Collection implements CollectionIface
{
    /** @var array<int,V> */
    private array $collection;

    /** @param array<int,V> $collection */
    protected function __construct(array $collection)
    {
        $this->collection = $collection;
    }

    abstract public static function getElementType(): Type\Definition;

    // #region Iterator
    /** @return V|false */
    protected function currentGeneric(): mixed
    {
        return \current($this->collection);
    }

    public function next(): void
    {
        \next($this->collection);
    }

    public function key(): ?int
    {
        return \key($this->collection);
    }

    public function valid(): bool
    {
        return \current($this->collection) !== false;
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

    /** @return array<int,V> */
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

    /** @return array<int,V> */
    public function toArray(): array
    {
        return $this->collection;
    }

    /** @param V $elements */
    protected function addGeneric(mixed ...$elements): void
    {
        foreach ($elements as $key => $element) {
            if (! static::getElementType()->isOfType($element)) {
                throw new \InvalidArgumentException(
                    Exc\Util\MsgProvider::createTypeErrMsg(
                        ((int) $key) + 1,
                        static::getElementType()->getTypeName(),
                        \get_debug_type($element),
                    )
                );
            }
            $this->collection[] = $element;
        }
    }

    /**
     * @no-named-arguments
     * @param V $elements
     */
    protected function removeGeneric(mixed ...$elements): void
    {
        foreach ($elements as $key => $elem) {
            if (! static::getElementType()->isOfType($elem)) {
                throw new \InvalidArgumentException(
                    Exc\Util\MsgProvider::createTypeErrMsg(
                        ++$key,
                        'elements',
                        static::getElementType()->getTypeName(),
                        \get_debug_type($elem),
                    )
                );
            }

            $strict = ! \is_object($elem);
            foreach ($this->collection as $key => $collectionElem) {
                $found = $strict ? $elem === $collectionElem : $elem == $collectionElem;
                if ($found) {
                    unset($this->collection[$key]);
                }
            }
        }
    }
}
