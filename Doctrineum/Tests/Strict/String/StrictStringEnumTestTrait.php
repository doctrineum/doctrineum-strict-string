<?php
namespace Doctrineum\Tests\Strict\String;

use Doctrineum\Strict\String\StrictStringEnum;

trait StrictStringEnumTestTrait
{
    /**
     * @return \Doctrineum\Strict\String\StrictStringEnum
     */
    protected function getEnumClass()
    {
        return preg_replace('~Test$~', '', static::class);
    }

    /**
     * @test
     */
    public function can_create_instance()
    {
        $enumClass = $this->getEnumClass();
        $instance = $enumClass::getEnum('foo');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf($enumClass, $instance);
    }

    /** @test */
    public function as_string_is_of_same_value_as_created_with()
    {
        $enumClass = $this->getEnumClass();
        $enum = $enumClass::getEnum('foo');
        /** @var \PHPUnit_Framework_TestCase $this */
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
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTestTrait $this */
        $this->assertInstanceOf($enumClass, $enum);
        $this->assertSame($value, $enum->getValue());
        $this->assertSame($value, (string)$enum);

        $inDifferentNamespace = $this->getInheritedEnum($value);
        $this->assertInstanceOf($enumClass, $inDifferentNamespace);
        $this->assertSame($enum->getValue(), $inDifferentNamespace->getValue());
        $this->assertNotSame($enum, $inDifferentNamespace);
    }

    /**
     * @param $value
     * @return StrictStringEnum
     */
    abstract protected function getInheritedEnum($value);
}
