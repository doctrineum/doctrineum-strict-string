<?php
namespace Doctrineum\Tests\Strict\String;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Scalar\EnumInterface;
use Doctrineum\Scalar\EnumType;

trait StrictStringEnumTypeTestTrait
{
    /**
     * @return \Doctrineum\Strict\String\StrictStringEnumType|\Doctrineum\Strict\String\SelfTypedStrictStringEnum
     */
    protected function getEnumTypeClass()
    {
        return preg_replace('~Test$~', '', static::class);
    }

    /**
     * @return \Doctrineum\Strict\String\StrictStringEnum|\Doctrineum\Strict\String\SelfTypedStrictStringEnum
     */
    protected function getRegisteredEnumClass()
    {
        return preg_replace('~(Type)?Test$~', '', static::class);
    }

    /**
     * @test
     */
    public function can_be_registered()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        Type::addType($enumTypeClass::getTypeName(), $enumTypeClass);
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertTrue(Type::hasType($enumTypeClass::getTypeName()));
    }

    /**
     * @return EnumType
     *
     * @test
     * @depends can_be_registered
     */
    public function instance_can_be_obtained()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $instance = $enumTypeClass::getType($enumTypeClass::getTypeName());
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf($enumTypeClass, $instance);

        return $instance;
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     */
    public function sql_declaration_is_valid(EnumType $enumType)
    {
        $sql = $enumType->getSQLDeclaration([], $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame('VARCHAR(64)', $sql);
    }

    /**
     * @return AbstractPlatform
     */
    private function getAbstractPlatform()
    {
        return \Mockery::mock(AbstractPlatform::class);
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     */
    public function type_name_is_as_expected(EnumType $enumType)
    {
        $enumTypeClass = $this->getEnumTypeClass();
        // like self_typed_strict_string_enum
        $typeName = $this->convertToTypeName($enumTypeClass);
        // like SELF_TYPED_STRICT_STRING_ENUM
        $constantName = strtoupper($typeName);
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $this->assertTrue(defined("$enumTypeClass::$constantName"), "Enum type class should has defined constant $enumTypeClass::$constantName");
        $this->assertSame($enumTypeClass::getTypeName(), $typeName);
        $this->assertSame($typeName, constant("$enumTypeClass::$constantName"));
        $this->assertSame($enumType::getTypeName(), $enumTypeClass::getTypeName());
    }

    /**
     * @param string $className
     * @return string
     */
    private function convertToTypeName($className)
    {
        $withoutType = preg_replace('~Type$~', '', $className);
        $parts = explode('\\', $withoutType);
        $baseClassName = $parts[count($parts) - 1];
        preg_match_all('~(?<words>[A-Z][^A-Z]+)~', $baseClassName, $matches);
        $concatenated = implode('_', $matches['words']);

        return strtolower($concatenated);
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     */
    public function enum_as_database_value_is_string_value_of_that_enum(EnumType $enumType)
    {
        $enum = \Mockery::mock(EnumInterface::class);
        $enum->shouldReceive('getEnumValue')
            ->once()
            ->andReturn($value = 'foo');
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        /** @var EnumInterface $enum */
        $this->assertSame($value, $enumType->convertToDatabaseValue($enum, $this->getAbstractPlatform()));
        \Mockery::close();
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     */
    public function string_to_php_value_is_enum_with_that_string(EnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($string = 'foo', $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $this->assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        $this->assertSame($string, $enum->getEnumValue());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     */
    public function empty_string_to_php_value_is_enum_with_that_empty_string(EnumType $enumType)
    {
        $enum = $enumType->convertToPHPValue($emptyString = '', $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $this->assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        $this->assertSame($emptyString, $enum->getEnumValue());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function null_to_php_value_causes_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(null, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function integer_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(12345, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function zero_integer_to_php_value_cause_exceptions(EnumType $enumType)
    {
        $enumType->convertToPHPValue(0, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function float_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(12345.6789, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function zero_float_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(0.0, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function false_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(false, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function true_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(true, $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function array_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue([], $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function resource_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(tmpfile(), $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function object_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(new \stdClass(), $this->getAbstractPlatform());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function callback_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(function () {
        }, $this->getAbstractPlatform());
    }

}
