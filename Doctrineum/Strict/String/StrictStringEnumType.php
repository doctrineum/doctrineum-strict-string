<?php
namespace Doctrineum\Strict\String;

use Doctrineum\Scalar\EnumType;

/**
 * Class EnumType
 * @package Doctrineum
 * @method static StrictStringEnumType getType($name),
 * @see EnumType::getType
 */
class StrictStringEnumType extends EnumType
{
    use StrictStringEnumTypeTrait;
}
