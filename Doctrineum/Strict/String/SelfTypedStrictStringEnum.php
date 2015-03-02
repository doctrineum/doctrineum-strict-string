<?php
namespace Doctrineum\Strict\String;

use Doctrineum\Scalar\SelfTypedEnum;

/**
 * @method static SelfTypedStrictStringEnum getType($name),
 * @see Type::getType
 */
class SelfTypedStrictStringEnum extends SelfTypedEnum
{

    use StrictStringEnumTypeTrait;

    /**
     * Type has private constructor, the only way how to create an Enum, which is also Type, is by Type factory method,
     * @see SelfTypedEnum::createByValue and its usage of
     * @see Type::getType
     *
     * @param string $enumValue
     * @return SelfTypedStrictStringEnum
     */
    protected static function createByValue($enumValue)
    {
        static::checkIfString($enumValue);
        $selfTypedEnum = parent::createByValue($enumValue);

        return $selfTypedEnum;
    }

    /**
     * @param mixed $value
     */
    protected static function checkIfString($value)
    {
        if (!is_string($value)) {
            throw new Exceptions\UnexpectedValueToEnum(
                'Expected string, got ' . gettype($value)
            );
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->enumValue;
    }

}
