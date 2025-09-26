<?php

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
