<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 13.11.2018
 * Time: 08:05
 */


namespace tbollmeier\ape\interpreter;

use tbollmeier\ape\object\Boolean;
use tbollmeier\ape\object\Integer;
use tbollmeier\ape\object\IObject;
use tbollmeier\ape\object\NullObject;
use tbollmeier\ape\parser\Parser;
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
        $this->assertStatement("null;", NullObject::getInstance());
        $this->assertStatement("true;", Boolean::getTrue());
        $this->assertStatement("false;", Boolean::getFalse());
        $this->assertStatement("!!true;", Boolean::getTrue());
        $this->assertStatement("!false;", Boolean::getTrue());
        $this->assertStatement("--42;", new Integer(42));
        $this->assertStatement("41+1;", new Integer(42));
        $this->assertStatement("43-1;", new Integer(42));
        $this->assertStatement("7*6;", new Integer(42));
        $this->assertStatement("85 / 2;", new Integer(42));
        $this->assertStatement("7*(2+3) + 7;", new Integer(42));
    }

    private function assertStatement(string $stmt, IObject $expResult)
    {

        $parser = new Parser();
        $ast = $parser->parseString($stmt);
        print($ast->toXml());

        $result = $this->interpreter->evalCode($stmt);
        $this->assertEquals($expResult->getType(), $result->getType());
        $this->assertEquals($expResult->toString(), $result->toString());

    }
}
