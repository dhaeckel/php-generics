<?php

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
