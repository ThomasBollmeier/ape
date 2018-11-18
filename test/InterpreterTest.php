<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 13.11.2018
 * Time: 08:05
 */


namespace tbollmeier\ape\interpreter;

require_once "../vendor/autoload.php";

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
41 + 1;
CODE;
        /*
        $parser = new \tbollmeier\ape\parser\Parser();
        $ast = $parser->parseString($code);
        print($ast->toXml());
        */

        $result = $this->interpreter->evalCode($code);
        print($result->toString());

    }
}
