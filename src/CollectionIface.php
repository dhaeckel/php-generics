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

/**
 * @template V
 * @extends \Iterator<int,V>
 */
interface CollectionIface extends \Countable, \Iterator, \JsonSerializable
{
    /**
     * @no-named-arguments
     * @param V $elements
     */
    public function add(mixed ...$elements): void;

    /**
     * @no-named-arguments
     * @param V $elements
     */
    public function remove(mixed ...$elements): void;

    /** @return array<int,V> */
    public function toArray(): array;

    public function isEmpty(): bool;

    public function clear(): void;
}
