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

    protected function setUp()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        if (Type::hasType($enumTypeClass::getTypeName())) {
            $enumTypeClass = $this->getEnumTypeClass();
            Type::overrideType($enumTypeClass::getTypeName(), $enumTypeClass);
        } else {
            $enumTypeClass = $this->getEnumTypeClass();
            Type::addType($enumTypeClass::getTypeName(), $enumTypeClass);
        }
    }

    /**
     * @return EnumType
     *
     * @test
     */
    public function instance_can_be_obtained()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $instance = $enumTypeClass::getType($enumTypeClass::getTypeName());
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf($enumTypeClass, $instance);

        return $instance;
    }

    /** @test */
    public function sql_declaration_is_valid()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
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
     * @test
     */
    public function enum_as_database_value_is_string_value_of_that_enum()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
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
     * @test
     */
    public function string_to_php_value_is_enum_with_that_string()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enum = $enumType->convertToPHPValue($string = 'foo', $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $this->assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        $this->assertSame($string, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function empty_string_to_php_value_is_enum_with_that_empty_string()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enum = $enumType->convertToPHPValue($emptyString = '', $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $this->assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        $this->assertSame($emptyString, $enum->getEnumValue());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function null_to_php_value_causes_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(null, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function integer_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(12345, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function zero_integer_to_php_value_cause_exceptions()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(0, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function float_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(12345.6789, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function zero_float_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(0.0, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function false_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(false, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function true_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(true, $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function array_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue([], $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function resource_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(tmpfile(), $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function object_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(new \stdClass(), $this->getAbstractPlatform());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function callback_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(function () {
        }, $this->getAbstractPlatform());
    }

}
