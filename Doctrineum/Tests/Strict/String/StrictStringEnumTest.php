<?php
namespace Doctrineum\Strict\String;

class StrictStringEnumTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function I_can_create_strict_string_enum()
    {
        $enumClass = $this->getEnumClass();
        $instance = $enumClass::getEnum('foo');
        $this->assertInstanceOf($enumClass, $instance);
        $this->assertInstanceOf('Granam\String\StringInterface', $instance);
    }

    /**
     * @return \Doctrineum\Strict\String\StrictStringEnum
     */
    protected function getEnumClass()
    {
        return StrictStringEnum::getClass();
    }

    /** @test */
    public function as_string_is_of_same_value_as_created_with()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('foo');
        $this->assertSame('foo', (string)$enum);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function null_as_non_string_cause_exception()
    {
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum(null);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function integer_zero_as_string_cause_exception()
    {
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum(0);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function float_zero_as_string_cause_exception()
    {
        $enumClass = $this->getEnumClass();
        $enumClass::getEnum(0.0);
    }


    /**
     * inner namespace test
     */

    /**
     * @test
     */
    public function inherited_enum_with_same_value_lives_in_own_inner_namespace()
    {
        $enumClass = $this->getEnumClass();

        $enum = $enumClass::getEnum($value = 'foo');
        $this->assertInstanceOf($enumClass, $enum);
        $this->assertSame($value, $enum->getEnumValue());
        $this->assertSame($value, (string)$enum);

        $inDifferentNamespace = $this->getInheritedEnum($value);
        $this->assertInstanceOf($enumClass, $inDifferentNamespace);
        $this->assertSame($enum->getEnumValue(), $inDifferentNamespace->getEnumValue());
        $this->assertNotSame($enum, $inDifferentNamespace);
    }

    protected function getInheritedEnum($value)
    {
        return new TestInheritedStrictStringEnum($value);
    }
}

/** inner */
class TestInheritedStrictStringEnum extends StrictStringEnum
{

}
