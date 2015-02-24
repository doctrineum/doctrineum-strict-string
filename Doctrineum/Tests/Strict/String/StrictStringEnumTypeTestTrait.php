<?php
namespace Doctrineum\Tests\Strict\String;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Generic\EnumInterface;

trait StrictStringEnumTypeTestTrait
{
    /**
     * @return \Doctrineum\Strict\String\StrictStringEnumType|\Doctrineum\Strict\String\SelfTypedStrictStringEnum
     */
    protected function getEnumTypeClass()
    {
        return preg_replace('~Test$~', '', static::class);
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

    /** @test */
    public function instance_can_be_obtained()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $instance = $enumTypeClass::getType($enumTypeClass::getTypeName());
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf($enumTypeClass, $instance);
    }

    /** @test */
    public function sql_declaration_is_valid()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $sql = $enumType->getSQLDeclaration([], \Mockery::mock(AbstractPlatform::class));
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertSame('VARCHAR(64)', $sql);
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
        /** @var \PHPUnit_Framework_TestCase $this */
        /** @var EnumInterface $enum */
        $this->assertSame($value, $enumType->convertToDatabaseValue($enum, \Mockery::mock(AbstractPlatform::class)));
        \Mockery::close();
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function null_to_php_value_causes_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(null, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     */
    public function string_to_php_value_is_enum_with_that_string()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enum = $enumType->convertToPHPValue($string = 'foo', \Mockery::mock(AbstractPlatform::class));
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf(EnumInterface::class, $enum);
        $this->assertSame($string, $enum->getEnumValue());
    }

    /**
     * @test
     */
    public function empty_string_to_php_value_is_enum_with_that_empty_string()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enum = $enumType->convertToPHPValue($emptyString = '', \Mockery::mock(AbstractPlatform::class));
        /** @var \PHPUnit_Framework_TestCase $this */
        $this->assertInstanceOf(EnumInterface::class, $enum);
        $this->assertSame($emptyString, $enum->getEnumValue());
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function integer_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(12345, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function zero_integer_to_php_value_cause_exceptions()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(0, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function float_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(12345.6789, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function zero_float_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(0.0, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function false_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(false, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function true_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(true, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function array_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue([], \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function resource_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(tmpfile(), \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function object_to_php_value_cause_exception()
    {
        $enumTypeClass = $this->getEnumTypeClass();
        $enumType = $enumTypeClass::getType($enumTypeClass::getTypeName());
        $enumType->convertToPHPValue(new \stdClass(), \Mockery::mock(AbstractPlatform::class));
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
        }, \Mockery::mock(AbstractPlatform::class));
    }

}
