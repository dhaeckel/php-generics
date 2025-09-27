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

/**
 * covers class, interface and enum types
 * @link https://php.net/manual/en/language.types.type-system.php#language.types.type-system.atomic.user-defined
 */
final class ClassLike implements Definition, \Stringable
{
    public function __construct(private readonly string $typeName)
    {
        if (
            ! \class_exists($typeName)
            && ! \interface_exists($typeName)
            && ! \enum_exists($typeName)
        ) {
            throw new \InvalidArgumentException(
                'given typeName ' . $typeName . ' is not a class, interface or enum that exists'
            );
        }
    }

    public function __toString(): string
    {
        return $this->getTypeName();
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    public function isOfType(mixed $value): bool
    {
        return $value instanceof $this->typeName;
    }
}
