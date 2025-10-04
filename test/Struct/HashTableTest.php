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

namespace Haeckel\Generics\Test\Struct;

use Haeckel\Generics\{Cmp, Struct\HashTable, Type};
use Haeckel\Generics\Struct\HashTable\Entry;
use Haeckel\Generics\Test\{
    TestType\Customer,
    TestType\CustomerIdentity,
    TestType\CustomerMap,
    TestType\Foo,
};
use Haeckel\Generics\Test\TestType\{ComparableCustomer, IntMap, StringableKey, StringableKeyMap};
use PHPUnit\Framework\{
    Attributes\CoversClass,
    Attributes\Small,
    Attributes\UsesClass,
    TestCase,
};

#[Small]
#[CoversClass(HashTable\Base::class)]
#[CoversClass(HashTable\Entry::class)]
#[UsesClass(Type\ClassLike::class)]
#[UsesClass(Type\Builtin::class)]
#[UsesClass(Cmp::class)]
final class HashTableTest extends TestCase
{
    public function testPutAcceptsValidTypes(): void
    {
        $map = new CustomerMap();

        $map->put(new CustomerIdentity('foo', 'C123'), new Customer());

        $this->assertEquals(
            [(new CustomerIdentity('foo', 'C123'))->getHash() => new Customer()],
            $map->toArray(),
        );
    }

    public function testJsonSerialize(): void
    {
        $map = new CustomerMap();

        $map->put(new CustomerIdentity('foo', 'C123'), new Customer('C123'));

        $this->assertEquals(
            [(new CustomerIdentity('foo', 'C123'))->getHash() => new Customer('C123')],
            $map->jsonSerialize(),
        );
    }

    public function testPutRejectsInvalidKeyType(): void
    {
        $map = new CustomerMap();

        $this->expectException(\InvalidArgumentException::class);
        $map->put((new CustomerIdentity('foo', 'C123'))->getHash(), new Customer());
    }

    public function testPutRejectsInvalidValueType(): void
    {
        $map = new CustomerMap();

        $this->expectException(\InvalidArgumentException::class);
        $map->put(new CustomerIdentity('foo', 'C123'), new Foo());
    }

    public function testPutIfAbsent(): void
    {
        $map = new CustomerMap();

        $map->put(new CustomerIdentity('foo', 'C123'), new Customer('C123'));
        $currVal = $map->putIfAbsent(new CustomerIdentity('foo', 'C456'), new Customer('C456'));

        $this->assertEquals(null, $currVal);
        $this->assertEquals(
            [
                (new CustomerIdentity('foo', 'C123'))->getHash() => new Customer('C123'),
                (new CustomerIdentity('foo', 'C456'))->getHash() => new Customer('C456'),
            ],
            $map->toArray(),
            'toArray failed',
        );
    }

    public function testPutIfAbsentKeyExists(): void
    {
        $map = new CustomerMap();

        $map->put(new CustomerIdentity('foo', 'C123'), new Customer('C123'));
        $currVal = $map->putIfAbsent(new CustomerIdentity('foo', 'C123'), new Customer('C456'));

        $this->assertEquals(new Customer('C123'), $currVal);
        $this->assertEquals(
            [
                (new CustomerIdentity('foo', 'C123'))->getHash() => new Customer('C123'),
            ],
            $map->toArray(),
        );
    }

    public function testPutIfAbsentRejectsInvalidKey(): void
    {
        $map = new CustomerMap();

        $this->expectException(\InvalidArgumentException::class);
        $map->putIfAbsent((new CustomerIdentity('foo', 'C123'))->getHash(), new Customer('C123'));
    }

    public function testPutIfAbsentRejectsInvalidValue(): void
    {
        $map = new CustomerMap();

        $this->expectException(\InvalidArgumentException::class);
        $map->putIfAbsent(new CustomerIdentity('foo', 'C123'), new Foo());
    }

    public function testRemove(): void
    {
        $map = new CustomerMap();

        $map->put(new CustomerIdentity('foo', 'C123'), new Customer('C123'));
        $currVal = $map->remove(new CustomerIdentity('foo', 'C123'));

        $this->assertEquals(new Customer('C123'), $currVal);
        $this->assertEquals([], $map->toArray());
    }

    public function testRemoveReturnsNullOnNotFound(): void
    {
        $map = new CustomerMap();

        $map->put(new CustomerIdentity('foo', 'C123'), new Customer('C123'));
        $currVal = $map->remove(new CustomerIdentity('foo', 'C234'));

        $this->assertEquals(null, $currVal);
        $this->assertEquals(
            [(new CustomerIdentity('foo', 'C123'))->getHash() => new Customer('C123')],
            $map->toArray(),
        );
    }

    public function testRemoveRejectsInvalidKeyType(): void
    {
        $map = new CustomerMap();
        $map->put(new CustomerIdentity('foo', 'C123'), new Customer());

        $this->expectException(\InvalidArgumentException::class);
        $map->remove((new CustomerIdentity('foo', 'C123'))->getHash());
    }

    public function testRemoveIfMappedToKey(): void
    {
        $map = new CustomerMap();

        $map->put(new CustomerIdentity('foo', 'C123'), new Customer('C123'));
        $map->put(new CustomerIdentity('foo', 'C456'), new Customer('C456'));
        $removed = $map->removeIfMappedToKey(
            new CustomerIdentity('foo', 'C123'),
            new Customer('C123'),
        );

        $this->assertEquals(true, $removed);
        $this->assertEquals(
            [(new CustomerIdentity('foo', 'C456'))->getHash() => new Customer('C456')],
            $map->toArray(),
        );

        $this->expectException(\InvalidArgumentException::class);
        $removed = $map->removeIfMappedToKey('C123', new Customer('C123'));

        $this->expectException(\InvalidArgumentException::class);
        $removed = $map->removeIfMappedToKey(new CustomerIdentity('foo', 'C123'), new Foo('C123'));
    }

    public function testRemoveIfMappedToKeyWithComparable(): void
    {
        $map = new CustomerMap();

        $map->put(new CustomerIdentity('foo', 'C123'), new ComparableCustomer('C123'));
        $map->put(new CustomerIdentity('foo', 'C456'), new ComparableCustomer('C456'));
        $removed = $map->removeIfMappedToKey(
            new CustomerIdentity('foo', 'C123'),
            new ComparableCustomer('C123'),
        );

        $this->assertEquals(true, $removed);
        $this->assertEquals(
            [(new CustomerIdentity('foo', 'C456'))->getHash() => new ComparableCustomer('C456')],
            $map->toArray(),
        );
    }

    public function testRemoveIfMappedToKeyWithScalarValue(): void
    {
        $map = new IntMap();

        $map->put('0', 0);
        $map->put('1', 1);
        $removed = $map->removeIfMappedToKey('1', 1);

        $this->assertEquals(true, $removed);
        $this->assertEquals(['0' => 0], $map->toArray());
    }

    public function testRemoveIfMappedToKeyKeyDoesNotExist(): void
    {
        $map = new CustomerMap();

        $map->put(new CustomerIdentity('foo', 'C123'), new Customer('C123'));
        $map->put(new CustomerIdentity('foo', 'C456'), new Customer('C456'));
        $removed = $map->removeIfMappedToKey(
            new CustomerIdentity('foo', 'C789'),
            new Customer('C789'),
        );

        $this->assertEquals(false, $removed);
        $this->assertEquals(
            [
                (new CustomerIdentity('foo', 'C123'))->getHash() => new Customer('C123'),
                (new CustomerIdentity('foo', 'C456'))->getHash() => new Customer('C456'),
            ],
            $map->toArray(),
        );
    }

    public function testRemoveIfMappedToKeyValueMismatch(): void
    {
        $map = new CustomerMap();
        $id1 = new CustomerIdentity('foo', 'C123');
        $id2 = new CustomerIdentity('foo', 'C456');
        $customer1 = new Customer('C123');
        $customer2 = new Customer('C456');

        $map->put($id1, $customer1);
        $map->put($id2, $customer2);

        $removed = $map->removeIfMappedToKey($id1, new Customer('C789'));

        $this->assertEquals(false, $removed);
        $this->assertEquals(
            [$id1->getHash() => $customer1, $id2->getHash() => $customer2],
            $map->toArray(),
        );
    }

    public function testReplace(): void
    {
        $map = new CustomerMap();
        $id1 = new CustomerIdentity('foo', 'C123');
        $id2 = new CustomerIdentity('foo', 'C456');
        $customer1 = new Customer('C123');
        $customer2 = new Customer('C456');

        $map->put($id1, $customer1);
        $map->put($id2, $customer2);

        $replaced = $map->replace(
            new CustomerIdentity('foo', 'C789'),
            new Customer('C789'),
        );

        $this->assertEquals(null, $replaced);
        $this->assertEquals(
            [
                (new CustomerIdentity('foo', 'C123'))->getHash() => new Customer('C123'),
                (new CustomerIdentity('foo', 'C456'))->getHash() => new Customer('C456'),
            ],
            $map->toArray(),
        );

        $replaced = $map->replace($id1, new Customer('C789'),);

        $this->assertEquals($customer1, $replaced);
        $this->assertEquals(
            [
                $id1->getHash() => new Customer('C789'),
                $id2->getHash() => new Customer('C456'),
            ],
            $map->toArray(),
        );

        $this->expectException(\InvalidArgumentException::class);
        $replaced = $map->replace('C789', new Customer('C789'));

        $this->expectException(\InvalidArgumentException::class);
        $replaced = $map->replace($id1, new Foo('C789'));
    }

    public function testIsEmpty(): void
    {
        $map = new CustomerMap();

        $map->put(new CustomerIdentity('foo', 'C123'), new Customer('C123'));
        $map->put(new CustomerIdentity('foo', 'C456'), new Customer('C456'));

        $this->assertEquals(false, $map->isEmpty());
        $map->clear();
        $this->assertEquals(true, $map->isEmpty());
    }

    public function testContains(): void
    {
        $map = new CustomerMap();

        $map->put(new CustomerIdentity('foo', 'C123'), new Customer('C123'));
        $map->put(new CustomerIdentity('foo', 'C456'), new Customer('C456'));

        $this->assertEquals(true, $map->contains(new Customer('C123')));
        $this->assertEquals(false, $map->contains(new Customer('C789')));

        $this->expectException(\InvalidArgumentException::class);
        $map->contains(new Foo());
    }

    public function testContainsKey(): void
    {
        $map = new CustomerMap();
        $id1 = new CustomerIdentity('foo', 'C123');
        $id2 = new CustomerIdentity('foo', 'C456');
        $customer1 = new Customer('C123');
        $customer2 = new Customer('C456');

        $map->put($id1, $customer1);
        $map->put($id2, $customer2);

        $this->assertEquals(true, $map->containsKey($id1));
        $this->assertEquals(false, $map->containsKey(new CustomerIdentity('foo', 'C789')));

        $this->expectException(\InvalidArgumentException::class);
        $map->containsKey('C123');
    }

    public function testToArrayPreserveKeys(): void
    {
        $map = new CustomerMap();
        $id1 = new CustomerIdentity('foo', 'C123');
        $id2 = new CustomerIdentity('foo', 'C456');
        $customer1 = new Customer('C123');
        $customer2 = new Customer('C456');

        $map->put($id1, $customer1);
        $map->put($id2, $customer2);

        $this->assertEquals(
            [
                $id1->getHash() => new Entry($id1, $customer1),
                $id2->getHash() => new Entry($id2, $customer2),
            ],
            $map->toArrayPreserveEntries(),
        );
    }

    public function testGet(): void
    {
        $map = new CustomerMap();
        $id1 = new CustomerIdentity('foo', 'C123');
        $id2 = new CustomerIdentity('foo', 'C456');
        $customer1 = new Customer('C123');

        $map->put($id1, $customer1);
        $map->put($id2, new Customer('C456'));

        $this->assertEquals($customer1, $map->get($id1));
        $this->assertEquals(null, $map->get(new CustomerIdentity('foo', 'C789')));
    }

    public function testGetKeys(): void
    {
        $map = new CustomerMap();
        $id1 = new CustomerIdentity('foo', 'C123');
        $id2 = new CustomerIdentity('foo', 'C456');
        $map->put($id1, new Customer('C123'));
        $map->put($id2, new Customer('C456'));

        $this->assertEquals([$id1, $id2], $map->getKeys());
    }

    public function testGetValues(): void
    {
        $map = new CustomerMap();
        $id1 = new CustomerIdentity('foo', 'C123');
        $id2 = new CustomerIdentity('foo', 'C456');
        $customer1 = new Customer('C123');
        $customer2 = new Customer('C456');

        $map->put($id1, $customer1);
        $map->put($id2, $customer2);

        $this->assertEquals([$customer1, $customer2], $map->getValues());
    }

    public function testCount(): void
    {
        $map = new CustomerMap();
        $this->assertEquals(0, \count($map));

        $id1 = new CustomerIdentity('foo', 'C123');
        $id2 = new CustomerIdentity('foo', 'C456');
        $customer1 = new Customer('C123');
        $customer2 = new Customer('C456');

        $map->put($id1, $customer1);
        $map->put($id2, $customer2);

        $this->assertEquals(2, \count($map));
    }

    public function testIteration(): void
    {
        $map = new CustomerMap();

        $id1 = new CustomerIdentity('foo', 'C123');
        $id2 = new CustomerIdentity('foo', 'C456');
        $customer1 = new Customer('C123');
        $customer2 = new Customer('C456');

        $map->put($id1, $customer1);
        $map->put($id2, $customer2);

        $i = 0;
        foreach ($map as $key => $val) {
            if ($i === 0) {
                $this->assertEquals($id1, $key);
                $this->assertEquals($customer1, $val);
            }
            if ($i === 1) {
                $this->assertEquals($id2, $key);
                $this->assertEquals($customer2, $val);
            }
            $i++;
        }
    }

    public function testKeyThrowsOnOutOfRange(): void
    {
        $map = new CustomerMap();

        $map->next();

        $this->expectException(\OutOfRangeException::class);
        $this->expectExceptionMessage('called key on empty hashTable');
        $map->key();
    }

    public function testKeyThrowsOnPointerMovedBeyondEnd(): void
    {
        $map = new CustomerMap();
        $id1 = new CustomerIdentity('foo', 'C123');
        $customer1 = new Customer('C123');

        $map->put($id1, $customer1);
        $map->next();
        $map->next();

        $this->expectException(\OutOfRangeException::class);
        $this->expectExceptionMessage('pointer moved beyond end of elements');
        $map->key();
    }

    public function testCurrentThrowsOnPointerMovedBeyondEnd(): void
    {
        $map = new CustomerMap();
        $id1 = new CustomerIdentity('foo', 'C123');
        $customer1 = new Customer('C123');

        $map->put($id1, $customer1);
        $map->next();
        $map->next();

        $this->expectException(\OutOfRangeException::class);
        $this->expectExceptionMessage('pointer moved beyond end of elements');
        $map->current();
    }

    public function testCurrentThrowsOnEmptyHashTable(): void
    {
        $map = new CustomerMap();

        $map->next();

        $this->expectException(\OutOfRangeException::class);
        $this->expectExceptionMessage('called current on empty hashTable');
        $map->current();
    }

    public function testReplaceIfOldValMatches(): void
    {
        $map = new CustomerMap();

        $id1 = new CustomerIdentity('foo', 'C123');
        $id2 = new CustomerIdentity('foo', 'C456');
        $customer1 = new Customer('C123');
        $customer2 = new Customer('C456');
        $customer3 = new Customer('C789');

        $map->put($id1, $customer1);
        $map->put($id2, $customer2);
        $replaced = $map->replaceIfOldValMatches($id1, $customer1, $customer3);

        $this->assertEquals(true, $replaced);
        $this->assertEquals(
            [$id1->getHash() => $customer3, $id2->getHash() => $customer2],
            $map->toArray(),
        );
        $replaced = $map->replaceIfOldValMatches(
            $id2,
            new Customer('C101112'),
            new Customer('C131415'),
        );
        $this->assertEquals(false, $replaced);
        $this->assertEquals(
            [$id1->getHash() => $customer3, $id2->getHash() => $customer2],
            $map->toArray(),
        );
    }

    public function testStringableKey(): void
    {
        $map = new StringableKeyMap();

        $map->put(new StringableKey('0'), 0);
        $map->put(new StringableKey('1'), 1);
        $hasKey = $map->containsKey(new StringableKey('1'));

        $this->assertEquals(true, $hasKey);
    }
}
