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

namespace Haeckel\Generics\Type;

/** @link https://www.php.net/manual/en/language.types.type-system.php#language.types.type-system.composite.union */
final class Union implements Definition, \Stringable
{
    /** @var array<int,Builtin|ClassLike|Intersection> */
    private array $types;

    /** @no-named-arguments */
    public function __construct(Builtin|ClassLike|Intersection ...$types)
    {
        $this->types = $types;
    }

    public function __toString(): string
    {
        return $this->getTypeName();
    }

    public function getTypeName(): string
    {
        $parts = [];
        foreach ($this->types as $type) {
            $parts[] = (
                $type instanceof Intersection
                ? "({$type->getTypeName()})"
                : $type->getTypeName()
            );
        }

        return \implode('|', $parts);
    }

    public function isOfType(mixed $value): bool
    {
        foreach ($this->types as $type) {
            if ($type->isOfType($value)) {
                return true;
            }
        }

        return false;
    }
}
