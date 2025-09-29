<?php

declare(strict_types=1);

namespace Haeckel\Generics\Filter;

/** @template TValue */
interface ValueFilter
{
    /**
     * return true if value matches filter, and false if not. May type check the passed value,
     * but this is not part of the interface.
     * @link https://www.php.net/manual/en/function.array-filter.php for the underlying concept
     *
     * @param TValue $val
     */
    public function __invoke(mixed $val): bool;
}
