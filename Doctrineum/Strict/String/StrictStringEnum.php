<?php
namespace Doctrineum\Strict\String;

use Doctrineum\Scalar\Enum;
use Doctrineum\Scalar\EnumInterface;

class StrictStringEnum extends Enum implements EnumInterface
{
    /**
     * @param string $enumValue
     */
    public function __construct($enumValue)
    {
        $this->checkIfString($enumValue);
        parent::__construct($enumValue);
    }

    /**
     * @param mixed $value
     */
    protected function checkIfString($value)
    {
        if (!is_string($value)) {
            throw new Exceptions\UnexpectedValueToEnum(
                'Expected string, got ' . gettype($value)
            );
        }
    }

    /**
     * Using own namespace to avoid conflicts with other enums
     *
     * @param string $enumValue
     * @param string $namespace
     * @return StrictStringEnum
     */
    public static function getEnum($enumValue, $namespace = __CLASS__)
    {
        return parent::getEnum($enumValue, $namespace);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->enumValue;
    }

}
