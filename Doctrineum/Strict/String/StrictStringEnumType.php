<?php
namespace Doctrineum\Strict\String;

use Doctrineum\Scalar\EnumType;

/**
 * Class EnumType
 * @package Doctrineum
 * @method static StrictStringEnumType getType($name),
 * @see EnumType::getType
 *
 * @method integer convertToDatabaseValue(\Doctrineum\Scalar\EnumInterface $enumValue, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
 * @see \Doctrineum\Scalar\EnumType::convertToDatabaseValue
 */
class StrictStringEnumType extends EnumType
{
    /**
     * Its not directly used this library - the exactly same value is generated and used by
     * @see \Doctrineum\Scalar\SelfTypedEnum::getTypeName
     *
     * This constant exists to follow Doctrine type conventions.
     */
    const STRICT_STRING_ENUM = 'strict_string_enum';

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

        /** @var StrictStringEnum $enumClass */
        return StrictStringEnum::getEnum($enumValue);
    }
}
