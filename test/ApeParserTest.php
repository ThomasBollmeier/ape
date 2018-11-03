<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 02.11.2018
 * Time: 22:29
 */

use tbollmeier\ape\ApeParser;
use PHPUnit\Framework\TestCase;

class ApeParserTest extends TestCase
{
    private $parser;

    protected function setUp()
    {
        parent::setUp();
        $this->parser = new ApeParser();
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
CODE;

        $ast = $this->parser->parseString($code);
        $this->assertNotFalse($ast, $this->parser->error());

        print $ast->toXml();

    }
}
