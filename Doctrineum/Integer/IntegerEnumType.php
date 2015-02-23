<?php
namespace Doctrineum\Integer;

use Doctrineum\Generic\EnumType;

/**
 * Class EnumType
 * @package Doctrineum
 * @method static IntegerEnumType getType($name),
 * @see Type::getType
 */
class IntegerEnumType extends EnumType
{
    use IntegerEnumTypeTrait;
}
