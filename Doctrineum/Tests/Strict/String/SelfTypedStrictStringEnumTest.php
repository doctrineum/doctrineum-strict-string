<?php
namespace Doctrineum\Strict\String;

use Doctrineum\Tests\Strict\String\StrictStringEnumTestTrait;
use Doctrineum\Tests\Strict\String\StrictStringEnumTypeTestTrait;

class SelfTypedStrictStringEnumTest extends \PHPUnit_Framework_TestCase
{
    use StrictStringEnumTestTrait;
    use StrictStringEnumTypeTestTrait;

    /**
     * @test
     */
    public function type_name_has_as_expected()
    {
        $this->assertSame('self_typed_strict_string_enum', SelfTypedStrictStringEnum::getTypeName());
        $selfTypedStrictStringEnum = SelfTypedStrictStringEnum::getType(SelfTypedStrictStringEnum::getTypeName());
        $this->assertSame($selfTypedStrictStringEnum::getTypeName(), SelfTypedStrictStringEnum::getTypeName());
    }

    /** @test */
    public function any_enum_namespace_is_accepted()
    {
        $this->markTestSkipped('Self typed strict string does not support enum namespace yet.');
    }

    /**
     * @test
     * @expectedException \Doctrineum\Generic\Exceptions\SelfTypedEnumConstantNamespaceChanged
     */
    public function changing_enum_namespace_cause_exception()
    {
        SelfTypedStrictStringEnum::getEnum('foo', 'non-default-namespace');
    }

}
