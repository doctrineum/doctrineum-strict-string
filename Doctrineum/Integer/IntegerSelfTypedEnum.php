<?php
namespace Doctrineum\Integer;

use Doctrineum\Generic\SelfTypedEnum;

/**
* @method static IntegerSelfTypedEnum getType($name),
 * @see SelfTypedEnum::getType
*/
class IntegerSelfTypedEnum extends SelfTypedEnum
{
    use IntegerEnumTrait;
    use IntegerEnumTypeTrait;

    /**
     * @param int $enumValue
     */
    public function __construct($enumValue)
    {
        try {
            parent::__construct($this->convertToInteger($enumValue));
        } /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */ catch (\Granam\Strict\String\Exceptions\Exception $exception) {
            throw new Exceptions\UnexpectedValueToEnum(
                'Expecting integer value only, got ' . gettype($enumValue), $exception->getCode(), $exception
            );
        }
    }
}
