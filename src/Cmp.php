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

use Haeckel\Generics\Cmp\EqualityTestable;

class Cmp
{
    public static function areEqual(mixed $a, mixed $b): bool
    {
        if (\gettype($a) !== \gettype($b)) {
            return false;
        }

        if (\is_object($a) && \is_object($b)) {
            if (\get_class($a) !== \get_class($b)) {
                return false;
            }

            return match (true) {
                $a instanceof EqualityTestable => $a->isEqualTo($b),
                // @phpstan-ignore equal.notAllowed (object cmp)
                default => $a == $b,
            };
        }

        return $a === $b;
    }
}
