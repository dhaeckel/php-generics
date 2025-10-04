<?php

declare(strict_types=1);

namespace Haeckel\Generics\Cmp;

enum CompareResult: int
{
    case Equal = 0;
    case GreaterThan = 1;
    case lessThan = -1;
}
