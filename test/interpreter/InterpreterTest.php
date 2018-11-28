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
        $this->assertResult("null;", NullObject::getInstance());
        $this->assertResult("true;", Boolean::getTrue());
        $this->assertResult("false;", Boolean::getFalse());
        $this->assertResult("!!true;", Boolean::getTrue());
        $this->assertResult("!false;", Boolean::getTrue());
        $this->assertResult("!null;", Boolean::getTrue());
        $this->assertResult("!42;", Boolean::getFalse());
        $this->assertResult("--42;", new Integer(42));
        $this->assertResult("41+1;", new Integer(42));
        $this->assertResult("43-1;", new Integer(42));
        $this->assertResult("7*6;", new Integer(42));
        $this->assertResult("85 / 2;", new Integer(42));
        $this->assertResult("7*(2+3) + 7;", new Integer(42));
        $this->assertResult("41 == 42;", Boolean::getFalse());
        $this->assertResult("41 + 1 == 42;", Boolean::getTrue());
        $this->assertResult("!(41 + 1 == 42);", Boolean::getFalse());
        $this->assertResult("41 + 1 == 42 && 3 > 4;", Boolean::getFalse());
        $this->assertResult("1 == 1 && 4 != 2*2 || 10 < 99;", Boolean::getTrue());
        $this->assertResult("1 == 1 && (4 != 2*2 || 10 < 99);", Boolean::getTrue());
        $this->assertResult("if (1 == 1) { 42; };", new Integer(42));
        $this->assertResult("if (1 == 2) { 42; };", NullObject::getInstance());
        $this->assertResult("if (1 == 2) { 23; } else { 42; };", new Integer(42));
    }

    private function assertResult(string $stmt, IObject $expResult)
    {

        $parser = new Parser();
        $ast = $parser->parseString($stmt);
        if ($ast === false) {
            print($parser->error() . "\n");
            return;
        }
        print($ast->toXml());

        $result = $this->interpreter->evalCode($stmt);
        $this->assertEquals($expResult->getType(), $result->getType());
        $this->assertEquals($expResult->toString(), $result->toString());

    }
}
