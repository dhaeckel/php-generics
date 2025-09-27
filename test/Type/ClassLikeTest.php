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

use Haeckel\Generics\Test\TestType\Bar;
use Haeckel\Generics\Test\TestType\Foo;
use Haeckel\Generics\Type;
use PHPUnit\Framework\{Attributes\CoversClass, TestCase};

#[CoversClass(Type\ClassLike::class)]
final class ClassLikeTest extends TestCase
{
    public function testGetName(): void
    {
        $classLike = new Type\ClassLike(Foo::class);
        $this->assertEquals(Foo::class, $classLike->getTypeName());
        $this->assertEquals(Foo::class, (string) $classLike);
    }

    public function testValidatesType(): void
    {
        $classLike = new Type\ClassLike(Foo::class);
        $this->assertEquals(true, $classLike->isOfType(new Foo()));
        $this->assertEquals(false, $classLike->isOfType(new Bar()));
        $this->assertEquals(false, $classLike->isOfType(1));
    }

    public function testThrowsOnNonExistingClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $classLike = new Type\ClassLike('Haeckel\Generic\Test\DoesNotExist');
    }
}
