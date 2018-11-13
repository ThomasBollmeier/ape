<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 13.11.2018
 * Time: 08:05
 */

namespace tbollmeier\ape\interpreter;

use PHPUnit\Framework\TestCase;
use tbollmeier\ape\parser\Parser;

class InterpreterTest extends TestCase
{
    private $parser;
    private $interpreter;

    protected function setUp()
    {
        parent::setUp();
        $this->parser = new Parser();
        $this->interpreter = new Interpreter();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->parser = null;
        $this->interpreter = null;
    }

    public function testEval()
    {

    }
}
