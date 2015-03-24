<?php
namespace Doctrineum\Tests\Strict\String;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrineum\Scalar\EnumInterface;
use Doctrineum\Scalar\EnumType;
use Doctrineum\Strict\String\StrictStringEnum;
use Doctrineum\Strict\String\StrictStringEnumType;

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

    protected function tearDown()
    {
        \Mockery::close();

        $enumTypeClass = $this->getEnumTypeClass();
        if (Type::hasType($enumTypeClass::getTypeName())) {
            $enumType = Type::getType($enumTypeClass::getTypeName(), $enumTypeClass);
            /** @var EnumType $enumType */
            if ($enumType::hasSubTypeEnum($this->getSubTypeEnumClass())) {
                /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
                $this->assertTrue($enumType::removeSubTypeEnum($this->getSubTypeEnumClass()));
            }
        }
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
        $enum->shouldReceive('getEnumValue')
            ->once()
            ->andReturn($value = 'foo');
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
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
        $enum = $enumType->convertToPHPValue($string = 'foo', $this->getAbstractPlatform());
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $this->assertInstanceOf($this->getRegisteredEnumClass(), $enum);
        $this->assertSame($string, $enum->getEnumValue());
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
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
        $enumType->convertToPHPValue(function () {
        }, $this->getAbstractPlatform());
    }

    /**
     * subtype tests
     */

    /**
     * @param EnumType $enumType
     *
     * @return EnumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     */
    public function can_register_subtype(EnumType $enumType)
    {
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $this->assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        $this->assertTrue($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()));

        return $enumType;
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function can_unregister_subtype(EnumType $enumType)
    {
        /**
         * @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this
         *
         * The subtype is unregistered because of tearDown clean up
         * @see StrictStringEnumTypeTestTrait::tearDown
         */
        $this->assertFalse($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()), 'Subtype should not be registered yet');
        $this->assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        $this->assertTrue($enumType::removeSubTypeEnum($this->getSubTypeEnumClass()));
        $this->assertFalse($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()));
    }

    /**
     * subtype tests
     */

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function subtype_returns_proper_enum(EnumType $enumType)
    {
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $this->assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~some specific string~'));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = \Mockery::mock(AbstractPlatform::class);
        $matchingValueToConvert = 'A string with some specific string inside.';
        $this->assertRegExp($regexp, $matchingValueToConvert);
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $subTypeEnum = $enumType->convertToPHPValue($matchingValueToConvert, $abstractPlatform);
        $this->assertInstanceOf($this->getSubTypeEnumClass(), $subTypeEnum);
        $this->assertSame("$matchingValueToConvert", "$subTypeEnum");
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends can_register_subtype
     */
    public function default_enum_is_given_if_subtype_does_not_match(EnumType $enumType)
    {
        /**
         * @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this
         */
        $this->assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~some specific string~'));
        /** @var AbstractPlatform $abstractPlatform */
        $abstractPlatform = \Mockery::mock(AbstractPlatform::class);
        $nonMatchingValueToConvert = 'A string without that specific string.';
        $this->assertNotRegExp($regexp, $nonMatchingValueToConvert);
        /**
         * Used TestSubtype returns as an "enum" the given value, which is $valueToConvert in this case,
         * @see \Doctrineum\Tests\Scalar\TestSubtype::getEnum
         */
        $enum = $enumType->convertToPHPValue($nonMatchingValueToConvert, $abstractPlatform);
        $this->assertNotSame($nonMatchingValueToConvert, $enum);
        $this->assertInstanceOf(EnumInterface::class, $enum);
        $this->assertSame($nonMatchingValueToConvert, (string)$enum);
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\SubTypeEnumIsAlreadyRegistered
     */
    public function registering_same_subtype_again_throws_exception(EnumType $enumType)
    {
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $this->assertFalse($enumType::hasSubTypeEnum($this->getSubTypeEnumClass()));
        $this->assertTrue($enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~'));
        // registering twice - should thrown an exception
        $enumType::addSubTypeEnum($this->getSubTypeEnumClass(), '~foo~');
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\InvalidClassForSubTypeEnum
     */
    public function registering_non_existing_subtype_class_throws_exception(EnumType $enumType)
    {
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $enumType::addSubTypeEnum('NonExistingClassName', '~foo~');
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\InvalidClassForSubTypeEnum
     */
    public function registering_subtype_class_without_proper_method_throws_exception(EnumType $enumType)
    {
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $enumType::addSubTypeEnum(\stdClass::class, '~foo~');
    }

    /**
     * @param EnumType $enumType
     *
     * @test
     * @depends type_instance_can_be_obtained
     * @expectedException \Doctrineum\Scalar\Exceptions\InvalidRegexpFormat
     * @expectedExceptionMessage The given regexp is not enclosed by same delimiters and therefore is not valid: 'foo~'
     */
    public function registering_subtype_with_invalid_regexp_throws_exception(EnumType $enumType)
    {
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        $enumType::addSubTypeEnum($this->getSubTypeEnumClass(), 'foo~' /* missing opening delimiter */);
    }


    /**
     * @test
     */
    public function can_register_another_enum_type()
    {
        $anotherEnumType = $this->getAnotherEnumTypeClass();
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        if (!$anotherEnumType::isRegistered()) {
            $this->assertTrue($anotherEnumType::registerSelf());
        } else {
            $this->assertFalse($anotherEnumType::registerSelf());
        }

        $this->assertTrue($anotherEnumType::isRegistered());
    }

    /**
     * @test
     *
     * @depends can_register_another_enum_type
     */
    public function different_types_with_same_subtype_regexp_distinguish_them()
    {
        /** @var EnumType $enumTypeClass */
        $enumTypeClass = $this->getEnumTypeClass();
        /** @var \PHPUnit_Framework_TestCase|StrictStringEnumTypeTestTrait $this */
        if ($enumTypeClass::hasSubTypeEnum($this->getSubTypeEnumClass())) {
            $enumTypeClass::removeSubTypeEnum($this->getSubTypeEnumClass());
        }
        $enumTypeClass::addSubTypeEnum($this->getSubTypeEnumClass(), $regexp = '~searching pattern~');

        $anotherEnumTypeClass = $this->getAnotherEnumTypeClass();
        if ($anotherEnumTypeClass::hasSubTypeEnum($this->getAnotherSubTypeEnumClass())) {
            $anotherEnumTypeClass::removeSubTypeEnum($this->getAnotherSubTypeEnumClass());
        }
        // regexp is same, sub-type is not
        $anotherEnumTypeClass::addSubTypeEnum($this->getAnotherSubTypeEnumClass(), $regexp);

        $value = 'some string fitting to searching pattern';
        $this->assertRegExp($regexp, $value);

        $enumType = $enumTypeClass::getIt();
        $enumSubType = $enumType->convertToPHPValue($value, $this->getPlatform());
        $this->assertInstanceOf($this->getSubTypeEnumClass(), $enumSubType);
        $this->assertSame($value, "$enumSubType");

        $anotherEnumType = $anotherEnumTypeClass::getIt();
        $anotherEnumSubType = $anotherEnumType->convertToPHPValue($value, $this->getPlatform());
        $this->assertInstanceOf($this->getSubTypeEnumClass(), $enumSubType);
        $this->assertSame($value, "$anotherEnumSubType");

        // registered sub-types were different, just regexp was the same - let's test if they are kept separately
        $this->assertNotSame($enumSubType, $anotherEnumSubType);
    }

    /**
     * @return AbstractPlatform
     */
    protected function getPlatform()
    {
        return \Mockery::mock(AbstractPlatform::class);
    }

    /**
     * @return string|TestAnotherStrictStringEnumType
     */
    protected function getAnotherEnumTypeClass()
    {
        return TestAnotherStrictStringEnumType::class;
    }

    /**
     * @return string|TestSubTypeStrictStringEnum
     */
    protected function getSubTypeEnumClass()
    {
        return TestSubTypeStrictStringEnum::class;
    }

    /**
     * @return string|TestAnotherSubTypeStrictStringEnum
     */
    protected function getAnotherSubTypeEnumClass()
    {
        return TestAnotherSubTypeStrictStringEnum::class;
    }

}

/** inner */
class TestSubTypeStrictStringEnum extends StrictStringEnum
{

}

class TestAnotherSubTypeStrictStringEnum extends StrictStringEnum
{

}

class TestAnotherStrictStringEnumType extends StrictStringEnumType
{

}
