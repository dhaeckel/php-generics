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

namespace Haeckel\Generics\Test;

use Haeckel\Generics\{
    Collection,
    Type,
    Util\InvArgExHelper,
};
use Haeckel\Generics\Test\TestType\{
    Bar,
    Foo,
    FooCollection,
    IntCollection,
};
use PHPUnit\Framework\{
    Attributes\Small,
    Attributes\CoversClass,
    Attributes\UsesClass,
    TestCase,
};

#[Small]
#[CoversClass(Collection::class)]
#[UsesClass(Type\ClassLike::class)]
#[UsesClass(Type\Builtin::class)]
class CollectionTest extends TestCase
{
    public function testAcceptsType(): void
    {
        $this->expectNotToPerformAssertions();
        new FooCollection(new Foo(), new Foo(), new Foo());
    }

    public function testIteration(): void
    {
        $collection = new FooCollection(new Foo(), new Foo(), new Foo());
        foreach ($collection as $key => $elem) {
            $this->assertIsInt($key);
            $this->assertInstanceOf(Foo::class, $elem);
        }
    }

    public function testAdd(): void
    {
        $collection = new FooCollection();
        $collection->add(new Foo());
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Argument #1 must be of type ' . Foo::class . ', ' . Bar::class . ' given',
        );
        $collection->add(new Bar());
    }

    public function testRemove(): void
    {
        $collection = new FooCollection(new Foo('a'));
        $collection->remove(new Foo('a'));
        $this->assertEquals([], $collection->toArray());
    }

    public function testRemoveWithUnequalVals(): void
    {
        $collection = new FooCollection(new Foo('a'));
        $collection->remove(new Foo('b'));
        $this->assertEquals([new Foo('a')], $collection->toArray());
    }

    public function testRemoveWithInvalidType(): void
    {
        $collection = new FooCollection(new Foo('a'));
        $this->expectException(\InvalidArgumentException::class);
        $collection->remove(new Bar('a'));
    }

    public function testRemoveWithMixedTypes(): void
    {
        $collection = new FooCollection(new Foo('a'));
        $this->expectException(\InvalidArgumentException::class);
        $collection->remove(new Foo('a'), 1);
    }

    public function testRemoveWithScalarType(): void
    {
        $collection = new IntCollection(1, 2, 3);
        $collection->remove(2, 5);
        $this->assertSame([0 => 1, 2 => 3], $collection->toArray());
    }

    public function testCount(): void
    {
        $coll = new FooCollection(new Foo(), new Foo(), new Foo());
        $count = $coll->count();
        $this->assertEquals(3, $count);
    }

    public function testJsonSerialize(): void
    {
        $coll = new FooCollection(new Foo('a'), new Foo('b'), new Foo('c'));

        $res = \json_encode($coll, \JSON_THROW_ON_ERROR);

        $this->assertJsonStringEqualsJsonString(
            \json_encode([['val' => 'a'], ['val' => 'b'], ['val' => 'c']]),
            $res,
        );
    }

    public function testClear(): void
    {
        $coll = new FooCollection(new Foo('a'), new Foo('b'), new Foo('c'));

        $coll->clear();

        $this->assertEquals([], $coll->toArray());
    }

    public function testIsEmpty(): void
    {
        $coll = new FooCollection(new Foo('a'), new Foo('b'), new Foo('c'));

        $this->assertEquals(false, $coll->isEmpty());
        $coll->clear();
        $this->assertEquals(true, $coll->isEmpty());
    }
}
