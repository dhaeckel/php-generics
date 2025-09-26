<?php

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
