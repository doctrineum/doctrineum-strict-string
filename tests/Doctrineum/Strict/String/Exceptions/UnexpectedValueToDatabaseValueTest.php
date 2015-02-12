<?php
namespace Doctrineum\Strict\String\Exceptions;

class UnexpectedValueToDatabaseValueTest extends \PHPUnit_Framework_TestCase {

    /**
     * @test
     * @expectedException \Doctrineum\Exceptions\UnexpectedValueToDatabaseValue
     */
    public function is_doctrineum_similar_exception()
    {
        throw new UnexpectedValueToDatabaseValue();
    }

    /**
     * @test
     * @expectedException \Doctrineum\Strict\String\Exceptions\Logic
     */
    public function is_local_logic_exception_exception()
    {
        throw new UnexpectedValueToDatabaseValue();
    }

}
