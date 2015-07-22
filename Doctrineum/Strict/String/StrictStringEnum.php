<?php
namespace Doctrineum\Strict\String;

use Doctrineum\Scalar\Enum;
use Doctrineum\Scalar\EnumInterface;
use Granam\Scalar\Tools\ValueDescriber;

/**
 * @method static StrictStringEnum $enumValue(string)
 */
class StrictStringEnum extends Enum implements EnumInterface
{

    protected static function convertToEnumFinalValue($value)
    {
        if (!is_string($value)) {
            throw new Exceptions\UnexpectedValueToEnum('Expected string, got ' . ValueDescriber::describe($value));
        }

        return $value;
    }

}
