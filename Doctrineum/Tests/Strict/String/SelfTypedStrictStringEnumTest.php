<?php
namespace Doctrineum\Strict\String;

use Doctrine\DBAL\Types\Type;
use Doctrineum\Tests\Strict\String\StrictStringEnumTestTrait;
use Doctrineum\Tests\Strict\String\StrictStringEnumTypeTestTrait;

class SelfTypedStrictStringEnumTest extends \PHPUnit_Framework_TestCase
{
    use StrictStringEnumTestTrait;
    use StrictStringEnumTypeTestTrait;

    protected function getInheritedEnum($value)
    {
        if (!Type::hasType(TestInheritedSelfTypedStrictStringEnum::getTypeName())) {
            TestInheritedSelfTypedStrictStringEnum::registerSelf();
        }
        $enum = TestInheritedSelfTypedStrictStringEnum::getEnum($value);

        return $enum;
    }
}

/** inner */
class TestInheritedSelfTypedStrictStringEnum extends SelfTypedStrictStringEnum
{

}
