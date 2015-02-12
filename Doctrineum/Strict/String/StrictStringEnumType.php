<?php
namespace Doctrineum\Strict\String;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrineum\EnumType;

/**
 * Class EnumType
 * @package Doctrineum
 * @method static StrictStringEnumType getType($name),
 * @see Type::getType
 */
class StrictStringEnumType extends EnumType
{
    const TYPE = 'strict-string-enum';

    const ENUM_CLASS = StrictStringEnum::class;

    /**
     * Convert enum instance to database string (or null) value
     *
     * @param StrictStringEnum $enumValue
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @throws Exceptions\UnexpectedValueToDatabaseValue
     * @return string|null
     */
    public function convertToDatabaseValue($enumValue, AbstractPlatform $platform)
    {
        if (!is_object($enumValue)) {
            throw new Exceptions\UnexpectedValueToDatabaseValue(
                'Expected object of class ' . StrictStringEnum::class . ', got ' . gettype($enumValue)
            );
        }
        if (!is_a($enumValue, StrictStringEnum::class)) {
            throw new Exceptions\UnexpectedValueToDatabaseValue(
                'Expected ' . StrictStringEnum::class . ', got ' . get_class($enumValue)
            );
        }

        return $enumValue->getValue();
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

        $enumClass = static::getEnumClass();
        /** @var StrictStringEnum $enumClass */
        return $enumClass::get($enumValue);
    }
}
