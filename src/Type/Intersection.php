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

namespace Haeckel\Generics\Type;

/** @link https://www.php.net/manual/en/language.types.type-system.php#language.types.type-system.composite.intersection */
final class Intersection implements Definition, \Stringable
{
    /** @var ClassLike[] */
    private array $intersection;

    public function __construct(ClassLike ...$definition)
    {
        $this->intersection = $definition;
    }

    public function __toString(): string
    {
        return $this->getTypeName();
    }

    public function isOfType(mixed $value): bool
    {
        foreach ($this->intersection as $type) {
            if (! $type->isOfType($value)) {
                return false;
            }
        }

        return true;
    }

    public function getTypeName(): string
    {
        return \implode('&', $this->intersection);
    }
}
