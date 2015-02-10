<?php
namespace Doctrineum\StrictString;

use Doctrineum\Enum;
use Doctrineum\EnumInterface;
use Granam\Strict\String\StrictStringTrait;

/**
 * Inspired by @link http://github.com/marc-mabe/php-enum
 */
class StrictStringEnum extends Enum implements EnumInterface
{
    use StrictStringTrait;

    /**
     * @param string $enumValue
     */
    public function __construct($enumValue)
    {
        try {
            $this->checkIfString($enumValue, true /* explicitly strict */);
            parent::__construct($this->convertToString($enumValue, true /* explicitly strict */));
        } catch (\Granam\Strict\String\Exceptions\Exception $exception) {
            throw new Exceptions\UnexpectedValueToEnum(
                'Expecting string, got ' . gettype($enumValue), $exception->getCode(), $exception
            );
        }
    }

    /**
     * @param string $enumValue
     * @param string $namespace
     * @return Enum
     */
    public static function get($enumValue, $namespace = __CLASS__)
    {
        return parent::get($enumValue, $namespace);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->enumValue;
    }

}
