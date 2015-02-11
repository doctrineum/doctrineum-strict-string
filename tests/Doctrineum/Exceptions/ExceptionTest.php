<?php
namespace Doctrineum\StrictString\Exceptions;

class ExceptionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function is_interface()
    {
        $this->assertTrue(interface_exists(Exception::class));
    }

    /**
     * @test
     * @expectedException \Doctrineum\Exceptions\Exception
     */
    public function extends_base_doctrineum_interface()
    {
        throw new TestLogicInterface();
    }

}

/** inner */
class TestExceptionInterface extends \Exception implements Exception
{

}
