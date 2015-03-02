<?php
namespace Doctrineum\Strict\String\Exceptions;

class UnexpectedValueToEnumTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @expectedException \Doctrineum\Scalar\Exceptions\UnexpectedValueToEnum
     */
    public function is_doctrineum_similar_exception()
    {
        throw new UnexpectedValueToEnum();
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\Logic
     */
    public function is_local_logic_exception()
    {
        throw new UnexpectedValueToEnum();
    }

}
