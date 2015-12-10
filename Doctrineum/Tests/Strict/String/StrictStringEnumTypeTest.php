<?php
namespace Doctrineum\Strict\String;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Scalar\EnumInterface;
use Doctrineum\Scalar\EnumType;

class StrictStringEnumTypeTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        \Mockery::close();

        $enumTypeClass = $this->getEnumTypeClass();
        if (Type::hasType($enumTypeClass::getTypeName())) {
            $enumType = Type::getType($enumTypeClass::getTypeName());
            /** @var EnumType $enumType */
            if ($enumType::hasSubTypeEnum($this->getSubTypeEnumClass())) {
                $this->assertTrue($enumType::removeSubTypeEnum($this->getSubTypeEnumClass()));
            }
        }
    }
    /**
     * @return \Doctrineum\Strict\String\StrictStringEnumType
     */
    private function getEnumTypeClass()
    {
        return StrictStringEnumType::getClass();
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
    public function type_instance_can_be_obtained()
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
     * @depends type_instance_can_be_obtained
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
     * @depends type_instance_can_be_obtained
     */
    public function type_name_is_as_expected(EnumType $enumType)
    {
        $enumTypeClass = $this->getEnumTypeClass();
        // like strict_string_enum
        $typeName = $this->convertToTypeName($enumTypeClass);
        // like STRICT_STRING_ENUM
        $constantName = strtoupper($typeName);
        $this->assertTrue(defined("$enumTypeClass::$constantName"), "Enum type class should has defined constant $enumTypeClass::$constantName");
        $this->assertSame($enumTypeClass::getTypeName(), $typeName);
        $this->assertSame($typeName, constant("$enumTypeClass::$constantName"));
        $this->assertSame($enumType::getTypeName(), $enumTypeClass::getTypeName());
    }

    /**
     * @param string $className
     *
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
     * @depends type_instance_can_be_obtained
     */
    public function enum_as_database_value_is_string_value_of_that_enum(EnumType $enumType)
    {
        $enum = \Mockery::mock(EnumInterface::class);
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $enum->shouldReceive('getEnumValue')
            ->once()
            ->andReturn($value = 'foo');
        /** @var EnumInterface $enum */
        $this->assertSame($value, $enumType->convertToDatabaseValue($enum, $this->getAbstractPlatform()));
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function string_to_php_value_is_enum_with_that_string(EnumType $enumType)
    {
        /** @var StrictStringEnum $enum */
        $enum = $enumType->convertToPHPValue($string = 'foo', $this->getAbstractPlatform());
        $this->assertInstanceOf(StrictStringEnum::getClass(), $enum);
        $this->assertSame($string, $enum->getValue());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function empty_string_to_php_value_is_enum_with_that_empty_string(EnumType $enumType)
    {
        /** @var StrictStringEnum $enum */
        $enum = $enumType->convertToPHPValue($emptyString = '', $this->getAbstractPlatform());
        $this->assertInstanceOf(StrictStringEnum::getClass(), $enum);
        $this->assertSame($emptyString, $enum->getValue());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
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
     * @depends type_instance_can_be_obtained
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
     * @depends type_instance_can_be_obtained
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
     * @depends type_instance_can_be_obtained
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
     * @depends type_instance_can_be_obtained
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
     * @depends type_instance_can_be_obtained
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
     * @depends type_instance_can_be_obtained
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
     * @depends type_instance_can_be_obtained
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
     * @depends type_instance_can_be_obtained
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
     * @depends type_instance_can_be_obtained
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
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function callback_to_php_value_cause_exception(EnumType $enumType)
    {
        $enumType->convertToPHPValue(
            function () {
            },
            $this->getAbstractPlatform()
        );
    }

    /**
     * @test
     */
    public function can_register_another_enum_type()
    {
        $anotherEnumType = $this->getAnotherEnumTypeClass();
        if (!$anotherEnumType::isRegistered()) {
            $this->assertTrue($anotherEnumType::registerSelf());
        } else {
            $this->assertFalse($anotherEnumType::registerSelf());
        }

        $this->assertTrue($anotherEnumType::isRegistered());
    }

    /**
     * @return string|TestAnotherStrictStringEnumType
     */
    private function getAnotherEnumTypeClass()
    {
        return TestAnotherStrictStringEnumType::class;
    }

    /**
     * @return string|TestSubTypeStrictStringEnum
     */
    private function getSubTypeEnumClass()
    {
        return TestSubTypeStrictStringEnum::class;
    }


}

/** inner */
class TestSubTypeStrictStringEnum extends StrictStringEnum
{

}

class TestAnotherStrictStringEnumType extends StrictStringEnumType
{

}
