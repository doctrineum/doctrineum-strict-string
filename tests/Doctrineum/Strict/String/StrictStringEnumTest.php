<?php
namespace Doctrineum\Strict\String;

class StrictStringEnumTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function can_create_instance()
    {
        $instance = StrictStringEnum::get('foo');
        $this->assertInstanceOf(StrictStringEnum::class, $instance);
    }

    /** @test */
    public function as_string_is_of_same_value_as_created_with()
    {
        $enum = StrictStringEnum::get('foo');
        $this->assertSame('foo', (string)$enum);
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\UnexpectedValueToEnum
     */
    public function non_string_cause_exception()
    {
        StrictStringEnum::get(null);
    }
}
