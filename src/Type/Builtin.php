<?php

/**
 * @copyright 2025 Dominik Häckel
 * @license LGPL-3.0-or-later
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

namespace Haeckel\Generics\Type;

/**
 * Language Builtin-types and pseudo types. See the enum cases for what's included and more info
 * @link https://www.php.net/manual/en/ref.var.php and there the is_* for the used functions
 */
enum Builtin: string implements Definition
{
    /** @link https://www.php.net/manual/en/language.types.null.php */
    case Null = 'null';
    /** @link https://www.php.net/manual/en/language.types.boolean.php */
    case Bool = 'bool';
    /** @link https://www.php.net/manual/en/language.types.integer.php */
    case Int = 'int';
    /** @link https://www.php.net/manual/en/language.types.float.php */
    case Float = 'float';
    /** @link https://www.php.net/manual/en/language.types.string.php */
    case String = 'string';
    /** @link https://www.php.net/manual/en/language.types.array.php */
    case Array = 'array';
    /** @link https://www.php.net/manual/en/language.types.object.php */
    case Object = 'object';
    /** @link https://www.php.net/manual/en/language.types.resource.php */
    case Resource = 'resource';
    /** @link https://www.php.net/manual/en/language.types.callable.php */
    case Callable = 'callable';
    /** @link https://www.php.net/manual/en/language.types.numeric-strings.php */
    case Numeric = 'numeric';
    /** @link https://www.php.net/manual/en/function.is-countable.php */
    case Countable = 'countable';
    /** @link https://www.php.net/manual/en/language.types.iterable.php */
    case Iterable = 'iterable';

    public function getTypeName(): string
    {
        return $this->value;
    }

    public function isOfType(mixed $value): bool
    {
        return match ($this) {
            self::Null => \is_null($value),
            self::Bool => \is_bool($value),
            self::Int => \is_int($value),
            self::Float => \is_float($value),
            self::String => \is_string($value),
            self::Array => \is_array($value),
            self::Object => \is_object($value),
            self::Resource => \is_resource($value),
            self::Callable => \is_callable($value),
            self::Numeric => \is_numeric($value),
            self::Countable => \is_countable($value),
            self::Iterable => \is_iterable($value),
        };
    }
}
