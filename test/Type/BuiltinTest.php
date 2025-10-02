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

use Haeckel\Generics\Type;
use PHPUnit\Framework\{
    Attributes\CoversClass,
    Attributes\Small,
    TestCase,
};

#[Small]
#[CoversClass(Type\Builtin::class)]
final class BuiltinTest extends TestCase
{
    public function testBoolValidation(): void
    {
        $type = Type\Builtin::Bool;
        $data = [1, true, false, 'true'];
        foreach ($data as $val) {
            $expect = \is_bool($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testIntValidation(): void
    {
        $arr = [\PHP_INT_MAX, \PHP_INT_MAX + 1, '1', true, \PHP_FLOAT_MAX, null];
        $type = Type\Builtin::Int;
        foreach ($arr as $val) {
            $expect = \is_int($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testFloatValidation(): void
    {
        $arr = [\PHP_INT_MAX, \PHP_FLOAT_MAX + 1, '1.0', true, \PHP_FLOAT_MAX, null];
        $type = Type\Builtin::Float;

        foreach ($arr as $val) {
            $expect = \is_float($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testStringValidation(): void
    {
        $arr = [1, '1', '', true, null];
        $type = Type\Builtin::String;

        foreach ($arr as $val) {
            $expect = \is_string($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testArrayValidation(): void
    {
        $arr = [1, '1', new \stdClass(), [], ['2', 3], true, null];
        $type = Type\Builtin::Array;

        foreach ($arr as $val) {
            $expect = \is_array($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testObjectValidation(): void
    {
        $arr = [1, '1', new \stdClass(), [''], true, null];
        $type = Type\Builtin::Object;

        foreach ($arr as $val) {
            $expect = \is_object($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testResourceValidation(): void
    {
        $arr = [\STDOUT, 1, []];
        $type = Type\Builtin::Resource;

        foreach ($arr as $val) {
            $expect = \is_resource($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testNullValidation(): void
    {
        $arr = [\STDOUT, null, [], 0, 0.0, ''];
        $type = Type\Builtin::Null;

        foreach ($arr as $val) {
            $expect = \is_null($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testNumericValidation(): void
    {
        $arr = [1, '2.0', 'a123', '1a', 1.0, null, ''];
        $type = Type\Builtin::Numeric;

        foreach ($arr as $val) {
            $expect = \is_numeric($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testCountableValidation(): void
    {
        $arr = [[], [1], new \ArrayObject(), '1', 1];
        $type = Type\Builtin::Countable;

        foreach ($arr as $val) {
            $expect = \is_countable($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testIterableValidation(): void
    {
        $gen = function (): \Generator {
            yield 1;
        };
        $arr = ['1', [], [1], $gen, null];
        $type = Type\Builtin::Iterable;

        foreach ($arr as $val) {
            $expect = \is_iterable($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testCallableValidation(): void
    {
        $arr = ['1', [], fn() => 1, true];
        $type = Type\Builtin::Callable;

        foreach ($arr as $val) {
            $expect = \is_callable($val);
            $this->assertEquals($expect, $type->isOfType($val));
        }
    }

    public function testGetName(): void
    {
        $type = Type\Builtin::Array;
        $this->assertEquals('array', $type->getTypeName());
        $type = Type\Builtin::Bool;
        $this->assertEquals('bool', $type->getTypeName());
        $type = Type\Builtin::Callable;
        $this->assertEquals('callable', $type->getTypeName());
        $type = Type\Builtin::Countable;
        $this->assertEquals('countable', $type->getTypeName());
        $type = Type\Builtin::Float;
        $this->assertEquals('float', $type->getTypeName());
        $type = Type\Builtin::Int;
        $this->assertEquals('int', $type->getTypeName());
        $type = Type\Builtin::Iterable;
        $this->assertEquals('iterable', $type->getTypeName());
        $type = Type\Builtin::Null;
        $this->assertEquals('null', $type->getTypeName());
        $type = Type\Builtin::Numeric;
        $this->assertEquals('numeric', $type->getTypeName());
        $type = Type\Builtin::Object;
        $this->assertEquals('object', $type->getTypeName());
        $type = Type\Builtin::Resource;
        $this->assertEquals('resource', $type->getTypeName());
        $type = Type\Builtin::String;
        $this->assertEquals('string', $type->getTypeName());
    }
}
