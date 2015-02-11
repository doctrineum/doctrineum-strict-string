<?php
namespace Doctrineum\StrictString\Exceptions;

class UnexpectedValueToEnumTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @expectedException \Doctrineum\Exceptions\UnexpectedValueToEnum
     */
    public function is_doctrineum_similar_exception()
    {
        throw new UnexpectedValueToEnum();
    }

    /**
     * @test
     * @expectedException \Doctrineum\StrictString\Exceptions\Logic
     */
    public function is_local_logic_exception()
    {
        throw new UnexpectedValueToEnum();
    }

}
