<?php
namespace Doctrineum\Strict\String;

use Doctrineum\Scalar\SelfTypedEnum;

/**
 * @method static SelfTypedStrictStringEnum getType($name),
 * @see Type::getType
 */
class SelfTypedStrictStringEnum extends SelfTypedEnum
{

    /**
     * Its not directly used this library - the exactly same value is generated and used by
     * @see \Doctrineum\Scalar\SelfTypedEnum::getTypeName
     *
     * This constant exists to follow Doctrine type conventions.
     */
    const SELF_TYPED_STRICT_STRING_ENUM = 'self_typed_strict_string_enum';

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
     * @param string $enumValue
     * @return StrictStringEnum
     */
    protected function convertToEnum($enumValue)
    {
        if (!is_string($enumValue)) {
            throw new Exceptions\UnexpectedValueToEnum(
                'Unexpected value to convert. Expected string, got ' . gettype($enumValue)
            );
        }

        return parent::convertToEnum($enumValue);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->enumValue;
    }

}
