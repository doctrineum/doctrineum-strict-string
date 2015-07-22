<?php
namespace Doctrineum\Strict\String;

use Doctrineum\Scalar\EnumType;

/**
 * Class EnumType
 * @package Doctrineum
 * @method static StrictStringEnumType getType($name),
 * @see EnumType::getType
 *
 * @method int convertToDatabaseValue(\Doctrineum\Scalar\EnumInterface $enumValue, \Doctrine\DBAL\Platforms\AbstractPlatform $platform)
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
     * @param $enumValue
     *
     * @return static
     */
    protected function convertToEnum($enumValue)
    {
        try {
            return parent::convertToEnum($enumValue);
        } catch (\Doctrineum\Scalar\Exceptions\UnexpectedValueToEnum $exception) {
            // wrapping by a local one
            throw new Exceptions\UnexpectedValueToEnum(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

}
