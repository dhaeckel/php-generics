<?php

/**
 * @copyright 2025 Dominik Häckel
 * @license LGPL-3.0-or-later
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

namespace Haeckel\Generics\Test\TestType;

use Haeckel\Generics\{Collection, Type};

class IntCollection extends Collection
{
    public function __construct(int ...$elements)
    {
        parent::__construct($elements);
    }

    public static function getElementType(): Type\Definition
    {
        return Type\Builtin::Int;
    }

    /** @var int ...$elements */
    public function add(mixed ...$elements): void
    {
        $this->addGeneric(...$elements);
    }

    /** @param int ...$elements */
    public function remove(mixed ...$elements): void
    {
        $this->removeGeneric(...$elements);
    }

    public function current(): int
    {
        return $this->currentGeneric();
    }
}
