<?php

declare(strict_types=1);

namespace Haeckel\Generics\Cmp;

/** @template TValue */
interface EqualityTestable
{
    /** @param TValue $to */
    public function isEqualTo(mixed $to): bool;
}
