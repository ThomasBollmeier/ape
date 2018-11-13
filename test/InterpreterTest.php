<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 13.11.2018
 * Time: 08:05
 */

namespace tbollmeier\ape\interpreter;

use PHPUnit\Framework\TestCase;

class InterpreterTest extends TestCase
{
    private $interpreter;

    protected function setUp()
    {
        parent::setUp();
        $this->interpreter = new Interpreter();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->interpreter = null;
    }

    public function testInterpret()
    {
        $code = <<<CODE
1+2;
CODE;
        $result = $this->interpreter->evalCode($code);
        print_r($result);

    }
}
