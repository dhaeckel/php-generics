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

use Haeckel\Exc\Util\MsgProvider;
use Haeckel\Generics\{
    Struct\Collection,
    Type,
};
use Haeckel\Generics\Filter\ValueFilter;
use Haeckel\Generics\Test\TestType\{
    Bar,
    Foo,
    FooCollection,
    IntCollection,
};
use OutOfRangeException;
use PHPUnit\Framework\{
    Attributes\Small,
    Attributes\CoversClass,
    Attributes\UsesClass,
    TestCase,
};

#[Small]
#[CoversClass(Collection\Base::class)]
#[UsesClass(Type\ClassLike::class)]
#[UsesClass(Type\Builtin::class)]
class CollectionTest extends TestCase
{
    public function testAcceptsAssignedType(): void
    {
        $this->expectNotToPerformAssertions();
        new FooCollection(new Foo(), new Foo(), new Foo());
    }

    public function testRejectsInvalidType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Argument #2 must be of type ' . Foo::class . ', ' . Bar::class . ' given',
        );
        new FooCollection(new Foo(), new Bar(), new Foo());
    }

    public function testIteration(): void
    {
        $collection = new FooCollection(new Foo(), new Foo(), new Foo());
        foreach ($collection as $key => $elem) {
            $this->assertIsInt($key);
            $this->assertInstanceOf(Foo::class, $elem);
        }
    }

    public function testForeachOnEmptyCollection(): void
    {
        $this->expectNotToPerformAssertions();
        $collection = new FooCollection();
        foreach ($collection as $key => $elem) {
        }
    }

    public function testThrowOnCurrenWhenEmptyCollection(): void
    {
        $this->expectException(\OutOfRangeException::class);
        $collection = new FooCollection();
        $collection->current();
    }

    public function testThrowOnCurrentWhenPointerBeyondElements(): void
    {
        $collection = new FooCollection(new Foo());
        $collection->next();
        $collection->next();
        $this->expectException(\OutOfRangeException::class);
        $collection->current();
    }

    public function testThrowOnKeyCallWhenEmptyCollection(): void
    {
        $this->expectException(\OutOfRangeException::class);
        $collection = new FooCollection();
        $collection->key();
    }

    public function testAdd(): void
    {
        $collection = new FooCollection();
        $collection->add(new Foo('b'));
        $this->assertEquals([new Foo('b')], $collection->toArray());
    }

    public function testAddWithInvalidValue(): void
    {
        $collection = new FooCollection();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Argument #2 must be of type ' . Foo::class . ', ' . Bar::class . ' given',
        );
        $collection->add(new Foo(), new Bar('b'), 1);
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

    public function testFind(): void
    {
        $coll = new FooCollection(new Foo('a'), new Foo('b'), new Foo('c'), new Foo('b'));
        $filter = new class implements ValueFilter {
            private Type\ClassLike $type;
            private Foo $target;

            public function __construct()
            {
                $this->type = new Type\ClassLike(Foo::class);
                $this->target = new Foo('b');
            }

            /** @param Foo $val */
            public function __invoke(mixed $val): bool
            {
                if (! $this->type->isOfType($val)) {
                    throw new \InvalidArgumentException(
                        MsgProvider::createTypeErrMsg(1, Foo::class, \get_debug_type($val), '$val'),
                    );
                }

                return $val->val === $this->target->val;
            }
        };
        $found = $coll->find($filter);

        $this->assertEquals([new Foo('b'), new Foo('b')], $found->toArray());
    }

    public function testFindFirst(): void
    {
        $coll = new FooCollection(new Foo('a'), new Foo('b'), new Foo('c'), new Foo('b'));
        $filter = new class implements ValueFilter {
            private Type\ClassLike $type;
            private Foo $target;

            public function __construct()
            {
                $this->type = new Type\ClassLike(Foo::class);
                $this->target = new Foo('b');
            }

            /** @param Foo $val */
            public function __invoke(mixed $val): bool
            {
                if (! $this->type->isOfType($val)) {
                    throw new \InvalidArgumentException(
                        MsgProvider::createTypeErrMsg(1, Foo::class, \get_debug_type($val), '$val'),
                    );
                }

                return $val->val === $this->target->val;
            }
        };
        $found = $coll->findFirst($filter);

        $this->assertEquals(new Foo('b'), $found);
    }

    public function testFindFirstNoMatches(): void
    {
        $coll = new IntCollection(1, 2, 3, 4, 5);
        $filter = /** @implements ValueFilter<int> */ new class implements ValueFilter {
            private Type\Builtin $type;

            public function __construct()
            {
                $this->type = Type\Builtin::Int;
            }

            /** @param int $val */
            public function __invoke(mixed $val): bool
            {
                if (! $this->type->isOfType($val)) {
                    throw new \InvalidArgumentException(
                        MsgProvider::createTypeErrMsg(1, Foo::class, \get_debug_type($val), '$val'),
                    );
                }

                return $val === 6;
            }
        };
        $found = $coll->findFirst($filter);

        $this->assertEquals(null, $found);
    }

    public function testRemoveIf(): void
    {
        $coll = new IntCollection(1, 2, 3, 4, 5);
        $filter = /** @implements ValueFilter<int> */ new class implements ValueFilter {
            private Type\Builtin $type;

            public function __construct()
            {
                $this->type = Type\Builtin::Int;
            }

            /** @param int $val */
            public function __invoke(mixed $val): bool
            {
                if (! $this->type->isOfType($val)) {
                    throw new \InvalidArgumentException(
                        MsgProvider::createTypeErrMsg(1, Foo::class, \get_debug_type($val), '$val'),
                    );
                }

                return $val === 3 || $val === 5;
            }
        };
        $coll->removeIf($filter);

        // keys are not preserved
        $this->assertEquals([0 => 1, 1 => 2, 3 => 4,], $coll->toArray());
    }
}
