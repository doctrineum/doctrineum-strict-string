<?php
namespace Doctrineum\Integer;

use Doctrineum\Generic\Enum;
use Granam\Strict\String\StrictStringTrait;

trait IntegerEnumTrait
{
    /** Adopting convertToString method
     * @see StrictStringTrait::convertToString
     */
    use StrictStringTrait;

    /**
     * @param $value
     * @return int
     */
    protected function convertToInteger($value)
    {
        if (is_int($value)) {
            return $value;
        }
        $stringValue = trim($this->convertToString($value, false /* not strict */));
        $integerValue = intval($stringValue);
        if ((string)$integerValue === $stringValue) { // the cast has been lossless
            return $integerValue;
        }
        throw new Exceptions\UnexpectedValueToEnum('Expecting integer value only, got ' . var_export($value, true));
    }

    /**
     * @param string $enumValue
     * @param string $namespace
     * @return Enum
     */
    public static function get($enumValue, $namespace = __CLASS__)
    {
        /** @var \Doctrineum\Generic\Enum parent */
        /** @noinspection PhpUndefinedClassInspection */
        return parent::get($enumValue, $namespace);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        /** @var \Doctrineum\Generic\Enum $this */
        return (string)$this->enumValue;
    }

}
