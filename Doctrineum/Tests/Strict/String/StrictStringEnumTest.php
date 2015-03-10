<?php
namespace Doctrineum\Strict\String;

use Doctrineum\Tests\Strict\String\StrictStringEnumTestTrait;

class StrictStringEnumTest extends \PHPUnit_Framework_TestCase
{
    use StrictStringEnumTestTrait;

    protected function getInheritedEnum($value)
    {
        return new TestInheritedStrictStringEnum($value);
    }
}

/** inner */
class TestInheritedStrictStringEnum extends StrictStringEnum
{

}
