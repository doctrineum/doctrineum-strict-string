<?php
namespace Doctrineum\Strict\String;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrineum\Scalar\EnumInterface;

/**
 * @method integer convertToDatabaseValue(EnumInterface $enumValue, AbstractPlatform $platform)
 * @see \Doctrineum\Scalar\EnumType::convertToDatabaseValue
 */
trait StrictStringEnumTypeTrait
{

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
        return $enumClass::getEnum($enumValue);
    }

    /**
     * @return string Enum class absolute name
     */
    protected static function getEnumClass()
    {
        return StrictStringEnum::class;
    }
}
