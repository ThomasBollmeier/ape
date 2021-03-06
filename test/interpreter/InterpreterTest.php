<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 13.11.2018
 * Time: 08:05
 */


namespace tbollmeier\ape\interpreter;

use tbollmeier\ape\object\ArrayObject;
use tbollmeier\ape\object\Boolean;
use tbollmeier\ape\object\Integer;
use tbollmeier\ape\object\IObject;
use tbollmeier\ape\object\NullObject;
use tbollmeier\ape\object\StringObject;
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
        $this->assertResult("null", NullObject::getInstance());
        $this->assertResult("true", Boolean::getTrue());
        $this->assertResult("false", Boolean::getFalse());
        $this->assertResult("!!true", Boolean::getTrue());
        $this->assertResult("!false", Boolean::getTrue());
        $this->assertResult("!null", Boolean::getTrue());
        $this->assertResult("!42", Boolean::getFalse());
        $this->assertResult("--42", new Integer(42));
        $this->assertResult("41+1", new Integer(42));
        $this->assertResult("43-1", new Integer(42));
        $this->assertResult("7*6", new Integer(42));
        $this->assertResult("85 / 2", new Integer(42));
        $this->assertResult("7*(2+3) + 7", new Integer(42));
        $this->assertResult("41 == 42", Boolean::getFalse());
        $this->assertResult("41 + 1 == 42", Boolean::getTrue());
        $this->assertResult("!(41 + 1 == 42)", Boolean::getFalse());
        $this->assertResult("41 + 1 == 42 && 3 > 4", Boolean::getFalse());
        $this->assertResult("1 == 1 && 4 != 2*2 || 10 < 99", Boolean::getTrue());
        $this->assertResult("1 == 1 && (4 != 2*2 || 10 < 99)", Boolean::getTrue());
        $this->assertResult("if (1 == 1) { 42 }", new Integer(42));
        $this->assertResult("if (1 == 2) { 42 }", NullObject::getInstance());
        $this->assertResult("if (1 == 2) { 23 } else { 42 };", new Integer(42));
        $this->assertResult("let answer = 42; answer", new Integer(42));

        $code = <<<CODE
let sum = fn (a, b) {
    return a + b;
};
sum(41, 1);
CODE;
        $this->assertResult($code, new Integer(42));

        $code = <<<CODE
let fact = fn (n) {
    if (n == 0) 1 else n * fact(n-1);
};
fact(5);
CODE;
        $this->assertResult($code, new Integer(120));

        $this->assertResult("let arr = [1, 2, 3]; arr",
            new ArrayObject([
                new Integer(1),
                new Integer(2),
                new Integer(3),
                ]));

        $this->assertResult("let arr = [1, 2, 42]; arr[2]",
            new Integer(42));

        $this->assertResult("let arr = [1, 2, 42]; arr[3]",
            NullObject::getInstance());

        $code = <<<CODE
let first_name = "Thomas";
let last_name = "Bollmeier";
let ego = first_name + " " + last_name;
ego;
CODE;
        $this->assertResult($code, new StringObject("Thomas Bollmeier"));

    }

    private function assertResult(string $stmt, IObject $expResult)
    {
        $parser = new Parser();
        $ast = $parser->parseString($stmt);
        if ($ast === false) {
            print($parser->error() . "\n");
            return;
        }
        //print($ast->toXml());

        $result = $this->interpreter->evalCode($stmt);
        $this->assertEquals($expResult->getType(), $result->getType(), $ast->toXml());
        $this->assertEquals($expResult->toString(), $result->toString());

    }
}
