<?php
namespace Doctrineum\StrictString\Exceptions;

class LogicTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function is_interface()
    {
        $this->assertTrue(interface_exists(Logic::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\Exception
     */
    public function extends_base_mark_interface()
    {
        throw new TestLogicInterface();
    }
}

/** inner */
class TestLogicInterface extends \Exception implements Logic
{

}
