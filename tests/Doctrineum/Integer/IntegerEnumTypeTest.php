<?php
namespace Doctrineum\Integer;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Granam\Strict\Object\Tests\StrictObjectTestTrait;

class IntegerEnumTypeTest extends \PHPUnit_Framework_TestCase
{
    use StrictObjectTestTrait;

    // SET UP

    protected function setUp()
    {
        if (\Doctrine\DBAL\Types\Type::hasType(IntegerEnumType::TYPE)) {
            \Doctrine\DBAL\Types\Type::overrideType(IntegerEnumType::TYPE, IntegerEnumType::class);
        } else {
            \Doctrine\DBAL\Types\Type::addType(IntegerEnumType::TYPE, IntegerEnumType::class);
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
        return IntegerEnumType::getType(IntegerEnumType::TYPE);
    }

    // TESTS THEMSELVES

    /** @test */
    public function instance_can_be_obtained()
    {
        $instance = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $this->assertInstanceOf(IntegerEnumType::class, $instance);
    }

    /** @test */
    public function sql_declaration_is_valid()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $sql = $enumType->getSQLDeclaration([], \Mockery::mock(AbstractPlatform::class));
        $this->assertSame('INTEGER', $sql);
    }

    /**
     * @test
     */
    public function enum_as_database_value_is_integer_value_of_that_enum()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enum = \Mockery::mock(IntegerEnum::class);
        $enum->shouldReceive('getValue')
            ->once()
            ->andReturn($value = 12345);
        $this->assertSame($value, $enumType->convertToDatabaseValue($enum, \Mockery::mock(AbstractPlatform::class)));
    }

    /**
     * @test
     */
    public function integer_to_php_value_gives_enum_with_that_integer()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enum = $enumType->convertToPHPValue($integer = 12345, \Mockery::mock(AbstractPlatform::class));
        $this->assertInstanceOf(IntegerEnum::class, $enum);
        $this->assertSame($integer, $enum->getValue());
        $this->assertSame("$integer", (string)$enum);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function string_integer_to_php_value_causes_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enumType->convertToPHPValue('12345', \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function null_to_php_value_causes_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enumType->convertToPHPValue(null, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function empty_string_to_php_value_causes_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enumType->convertToPHPValue('', \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function float_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enumType->convertToPHPValue(12345.6789, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function zero_float_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enumType->convertToPHPValue(0.0, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function false_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enumType->convertToPHPValue(false, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function true_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enumType->convertToPHPValue(true, \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function array_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enumType->convertToPHPValue([], \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function resource_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enumType->convertToPHPValue(tmpfile(), \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function object_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enumType->convertToPHPValue(new \stdClass(), \Mockery::mock(AbstractPlatform::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Integer\Exceptions\UnexpectedValueToEnum
     */
    public function callback_to_php_value_cause_exception()
    {
        $enumType = IntegerEnumType::getType(IntegerEnumType::TYPE);
        $enumType->convertToPHPValue(function () {
        }, \Mockery::mock(AbstractPlatform::class));
    }
}
