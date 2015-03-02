<?php
namespace Doctrineum\Tests\Strict\String;

trait StrictStringEnumTestTrait
{
    /**
     * @return \Doctrineum\Strict\String\StrictStringEnum|\Doctrineum\Strict\String\SelfTypedStrictStringEnum
     */
    protected function getEnumClass()
    {
        return preg_replace('~Test$~', '', static::class);
    }

    /** @test */
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

    /** @test */
    public function any_enum_namespace_is_accepted()
    {
        $strictStringEnumClass = $this->getEnumClass();
        $strictStringEnum = $strictStringEnumClass::getEnum($value = 'foo', $namespace = 'bar');
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf($strictStringEnumClass, $strictStringEnum);
        $this->assertSame($value, $strictStringEnum->getEnumValue());
        $this->assertSame($value, (string)$strictStringEnum);
        $inDifferentNamespace = $strictStringEnumClass::getEnum($value, $namespace . 'baz');
        $this->assertInstanceOf($strictStringEnumClass, $inDifferentNamespace);
        $this->assertNotSame($strictStringEnum, $inDifferentNamespace);
    }
}
