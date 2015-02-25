<?php
namespace Doctrineum\Strict\String;

use Doctrineum\Generic\SelfTypedEnum;

/**
 * @method static SelfTypedStrictStringEnum getType($name),
 * @see Type::getType
 */
class SelfTypedStrictStringEnum extends SelfTypedEnum
{

    use StrictStringEnumTypeTrait;

    /**
     * Using own namespace to avoid conflicts with other enums
     *
     * @param string $enumValue
     * @param string $namespace
     * @return SelfTypedStrictStringEnum
     */
    public static function getEnum($enumValue, $namespace = __CLASS__)
    {
        return parent::getEnum($enumValue, $namespace);
    }

    /**
     * Type has private constructor, the only way how to create an Enum, which is also Type, is by Type factory method,
     * @see Type::getType
     *
     * Overloaded parent @see \Doctrineum\Generic\EnumTrait::createByValue
     *
     * @param string $enumValue
     * @return SelfTypedStrictStringEnum
     */
    protected static function createByValue($enumValue)
    {
        static::checkIfString($enumValue);
        $selfTypedEnum = static::getType(static::getTypeName());
        $selfTypedEnum->enumValue = $enumValue;

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
     * Core idea of self-typed enum.
     * As an enum class returns itself.
     *
     * @return string
     */
    protected static function getEnumClass()
    {
        return static::class;
    }

    /**
     * Gets the strongly recommended name of this type.
     * Its used at @see \Doctrine\DBAL\Platforms\AbstractPlatform::getDoctrineTypeComment
     * @see EnumType::getName for direct usage
     *
     * @return string
     */
    public static function getTypeName()
    {
        return 'self-typed-strict-string-enum';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->enumValue;
    }

}
