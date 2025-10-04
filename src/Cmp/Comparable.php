<?php

declare(strict_types=1);

namespace Haeckel\Generics\Cmp;

/** @template TValue */
interface Comparable
{
    /** @param TValue $to */
    public function compare(mixed $to): CompareResult;
}
