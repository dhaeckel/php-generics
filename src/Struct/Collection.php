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

use Haeckel\Generics\Filter\ValueFilter;

/**
 * @template TValue
 * @extends \Iterator<int,TValue>
 */
interface Collection extends \Countable, \Iterator, \JsonSerializable
{
    /**
     * @no-named-arguments
     * @param TValue $elements
     */
    public function add(mixed ...$elements): void;

    /**
     * @no-named-arguments
     * @param TValue $elements
     */
    public function remove(mixed ...$elements): void;

    /** @return array<int,TValue> */
    public function toArray(): array;

    public function isEmpty(): bool;

    public function clear(): void;

    /**
     * find elements matching a filter
     *
     * @param ValueFilter<TValue> $filter
     */
    public function find(ValueFilter $filter): static;

    /**
     * @param ValueFilter<TValue> $filter
     *
     * @return TValue
     */
    public function findFirst(ValueFilter $filter): mixed;

    /** @param ValueFilter<TValue> $filter */
    public function removeIf(ValueFilter $filter): void;
}
