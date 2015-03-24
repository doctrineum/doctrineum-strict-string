<?php
namespace Doctrineum\Strict\String;

use Doctrineum\Scalar\Enum;
use Doctrineum\Scalar\EnumInterface;

/**
 * @method static StrictStringEnum $enumValue(string)
 */
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
     * @return string
     */
    public function __toString()
    {
        return $this->enumValue;
    }

}
