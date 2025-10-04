<?php

declare(strict_types=1);

namespace Haeckel\Generics;

use Haeckel\Generics\Cmp\EqualityTestable;

class Cmp
{
    public static function areEqual(mixed $a, mixed $b): bool
    {
        if (\gettype($a) !== \gettype($b)) {
            return false;
        }

        if (\is_object($a) && \is_object($b)) {
            if (\get_class($a) !== \get_class($b)) {
                return false;
            }

            return match (true) {
                $a instanceof EqualityTestable => $a->isEqualTo($b),
                // @phpstan-ignore equal.notAllowed (object cmp)
                default => $a == $b,
            };
        }

        return $a === $b;
    }
}
