<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 02.11.2018
 * Time: 22:29
 */

use tbollmeier\ape\parser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    private $parser;

    protected function setUp()
    {
        parent::setUp();
        $this->parser = new Parser();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->parser = null;
    }

    public function testProgram() {

        $code = <<<CODE
let answer = (1 + 2 + 4) * 2 * 3;
return 23;
let add = fn (a,b) {
    let x = a;
    let y = b;
    return x +y;
};
let arr = [1, 2*1, 12/4];
let ego = {
    "firstName": "Thomas",
    "lastName": "Bollmeier"
};
let myName = ego["firstName"];
fn (a, b) { return a*b; }(7,6);
getPerson()["lastName"];

if (isInitial) {
    doInit();
};

if (started) stop();

if (isEven(i)) {
    doEvenThings();
} else if (isOdd(i)) {
    doOddThings();
} else {
    handleError();
};

CODE;

        $ast = $this->parser->parseString($code);
        $this->assertNotFalse($ast, $this->parser->error());

        print $ast->toXml();

    }
}