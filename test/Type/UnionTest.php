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

use Haeckel\Generics\Test\TestType\{BarInterface, Foo, Foobar, FooInterface, Fractal};
use Haeckel\Generics\Type;
use PHPUnit\Framework\{
    Attributes\CoversClass,
    Attributes\Small,
    Attributes\UsesClass,
    TestCase,
};

#[Small]
#[CoversClass(Type\Union::class)]
#[UsesClass(Type\ClassLike::class)]
#[UsesClass(Type\Builtin::class)]
#[UsesClass(Type\Intersection::class)]
final class UnionTest extends TestCase
{
    public function testValidatesTypes(): void
    {
        $union = new Type\Union(
            Type\Builtin::Int,
            new Type\ClassLike(Foo::class),
            new Type\Intersection(
                new Type\ClassLike(FooInterface::class),
                new Type\ClassLike(BarInterface::class),
            ),
        );
        $this->assertEquals(true, $union->isOfType(1));
        $this->assertEquals(true, $union->isOfType(new Foo()));
        $this->assertEquals(true, $union->isOfType(new Foobar()));
        $this->assertEquals(false, $union->isOfType(true));
        $this->assertEquals(false, $union->isOfType(new Fractal()));
    }

    public function testStringRepresentation()
    {
        $union = new Type\Union(
            Type\Builtin::Int,
            new Type\ClassLike(Foo::class),
            new Type\Intersection(
                new Type\ClassLike(FooInterface::class),
                new Type\ClassLike(BarInterface::class),
            ),
        );

        $this->assertEquals(
            'int|' . Foo::class . '|(' . FooInterface::class . '&' . BarInterface::class . ')',
            $union->getTypeName(),
        );
        $this->assertEquals(
            'int|' . Foo::class . '|(' . FooInterface::class . '&' . BarInterface::class . ')',
            (string) $union,
        );
    }
}
