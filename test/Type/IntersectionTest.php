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

namespace Haeckel\Generics\Test\Type;

use Haeckel\Generics\Test\TestType\BarInterface;
use Haeckel\Generics\Test\TestType\Foo;
use Haeckel\Generics\Test\TestType\Foobar;
use Haeckel\Generics\Test\TestType\FooInterface;
use Haeckel\Generics\Test\TestType\Fractal;
use Haeckel\Generics\Type;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Type\Intersection::class)]
#[UsesClass(Type\ClassLike::class)]
final class IntersectionTest extends TestCase
{
    public function testValidatesType(): void
    {
        $intersection = new Type\Intersection(
            new Type\ClassLike(FooInterface::class),
            new Type\ClassLike(BarInterface::class)
        );
        $this->assertEquals(true, $intersection->isOfType(new Foobar()));
        $this->assertEquals(false, $intersection->isOfType(new Foo()));
        $this->assertEquals(false, $intersection->isOfType(new Fractal()));
        $this->assertEquals(false, $intersection->isOfType(1));
    }

    public function testGetTypeName(): void
    {
        $intersection = new Type\Intersection(
            new Type\ClassLike(FooInterface::class),
            new Type\ClassLike(BarInterface::class),
            new Type\ClassLike(Foo::class),
        );

        $this->assertEquals(
            FooInterface::class . '&' . BarInterface::class . '&' . Foo::class,
            $intersection->getTypeName(),
        );

        $this->assertEquals(
            FooInterface::class . '&' . BarInterface::class . '&' . Foo::class,
            (string) $intersection,
        );
    }
}
