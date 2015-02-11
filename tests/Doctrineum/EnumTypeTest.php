<?php
namespace Doctrineum\StrictString;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Granam\Strict\Object\Tests\StrictObjectTestTrait;

class EnumTypeTest extends \PHPUnit_Framework_TestCase
{
    use StrictObjectTestTrait;

    // SET UP

    protected function setUp()
    {
        if (\Doctrine\DBAL\Types\Type::hasType(StrictStringEnumType::TYPE)) {
            \Doctrine\DBAL\Types\Type::overrideType(StrictStringEnumType::TYPE, StrictStringEnumType::class);
        } else {
            \Doctrine\DBAL\Types\Type::addType(StrictStringEnumType::TYPE, StrictStringEnumType::class);
        }
    }

    protected function tearDown()
    {
        \Mockery::close();
    }

    /**
     * @return \Doctrine\DBAL\Types\Type
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function createObjectInstance()
    {
        return StrictStringEnumType::getType(StrictStringEnumType::TYPE);
    }

    // TESTS THEMSELVES

    /** @test */
    public function instance_can_be_obtained()
    {
        $instance = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $this->assertInstanceOf(StrictStringEnumType::class, $instance);
    }

    /** @test */
    public function sql_declaration_is_valid()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $sql = $enumType->getSQLDeclaration([], \Mockery::mock(AbstractPlatform::class));
        $this->assertSame('VARCHAR(64)', $sql);
    }

    /**
     * @test
     */
    public function enum_as_database_value_is_string_value_of_that_enum()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enum = \Mockery::mock(StrictStringEnum::class);
        $enum->shouldReceive('getValue')
            ->once()
            ->andReturn($value = 'foo');
        $this->assertSame($value, $enumType->convertToDatabaseValue($enum, \Mockery::mock(AbstractPlatform::class)));
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\UnexpectedValueToEnum
     */
    public function null_to_php_value_causes_exception()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enumType->convertToPHPValue(null, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     */
    public function string_to_php_value_is_enum_with_that_string()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enum = $enumType->convertToPHPValue($string = 'foo', \Mockery::mock(AbstractPlatform::class));
        $this->assertInstanceOf(StrictStringEnum::class, $enum);
        $this->assertSame($string, $enum->getValue());
    }

    /**
     * @test
     */
    public function empty_string_to_php_value_is_enum_with_that_empty_string()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enum = $enumType->convertToPHPValue($emptyString = '', \Mockery::mock(AbstractPlatform::class));
        $this->assertInstanceOf(StrictStringEnum::class, $enum);
        $this->assertSame($emptyString, $enum->getValue());
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\UnexpectedValueToEnum
     */
    public function integer_to_php_value_cause_exception()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enumType->convertToPHPValue(12345, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\UnexpectedValueToEnum
     */
    public function zero_integer_to_php_value_cause_exceptions()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enumType->convertToPHPValue(0, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\UnexpectedValueToEnum
     */
    public function float_to_php_value_cause_exception()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
         $enumType->convertToPHPValue(12345.6789, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\UnexpectedValueToEnum
     */
    public function zero_float_to_php_value_cause_exception()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enumType->convertToPHPValue(0.0, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\UnexpectedValueToEnum
     */
    public function false_to_php_value_cause_exception()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enumType->convertToPHPValue(false, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\UnexpectedValueToEnum
     */
    public function true_to_php_value_cause_exception()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enumType->convertToPHPValue(true, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\UnexpectedValueToEnum
     */
    public function array_to_php_value_cause_exception()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enumType->convertToPHPValue([], \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\UnexpectedValueToEnum
     */
    public function resource_to_php_value_cause_exception()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enumType->convertToPHPValue(tmpfile(), \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\UnexpectedValueToEnum
     */
    public function object_to_php_value_cause_exception()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enumType->convertToPHPValue(new \stdClass(), \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\UnexpectedValueToEnum
     */
    public function callback_to_php_value_cause_exception()
    {
        $enumType = StrictStringEnumType::getType(StrictStringEnumType::TYPE);
        $enumType->convertToPHPValue(function () {
        }, \Mockery::mock(AbstractPlatform::class));
    }
}
