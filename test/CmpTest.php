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

namespace Haeckel\Generics\Test;

use Haeckel\Generics\Cmp;
use Haeckel\Generics\Test\TestType\ComparableCustomer;
use Haeckel\Generics\Test\TestType\Customer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
#[CoversClass(Cmp::class)]
final class CmpTest extends TestCase
{
    public function testScalarCmp()
    {
        $this->assertSame(true, Cmp::areEqual(1, 1));
        $this->assertSame(false, Cmp::areEqual(1, '1'));
        $this->assertSame(
            true,
            Cmp::areEqual(new ComparableCustomer('C123'), new ComparableCustomer('C123')),
        );
        $this->assertSame(
            false,
            Cmp::areEqual(new ComparableCustomer('C123'), new ComparableCustomer('C456')),
        );
        $this->assertSame(
            false,
            Cmp::areEqual(new ComparableCustomer('C123'), new Customer('C123')),
        );
        $this->assertSame(
            true,
            Cmp::areEqual(new Customer('C123'), new Customer('C123')),
        );
        $this->assertSame(
            false,
            Cmp::areEqual(new Customer('C123'), new Customer('C456')),
        );
    }
}
