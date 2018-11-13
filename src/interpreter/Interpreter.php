<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 12.11.2018
 * Time: 22:06
 */

namespace tbollmeier\ape\interpreter;


use tbollmeier\ape\object\IObject;
use tbollmeier\ape\object\NullObject;
use tbollmeier\ape\object\ObjectType;
use tbollmeier\ape\parser\Parser;
use tbollmeier\parsian\output\Ast;

class Interpreter
{
    private $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function evalCode(string $program) : IObject
    {
        $ast = $this->parser->parseString($program);
        if ($ast !== false) {
            return $this->eval($ast, new Environment());
        } else {
            return new NullObject(); // TODO: return Error object
        }
    }


    private function eval(Ast $ast, Environment $env) : IObject
    {
        $name = $ast->getName();

        switch ($name) {
            case "ape":
                return $this->evalProgram($ast, $env);
        }

        return new NullObject();
    }

    private function evalProgram(Ast $program, Environment $env) : IObject
    {
        $statements = $program->getChildren();
        $result = $this->evalBlock($statements, $env);
        if ($result->getType() === ObjectType::RETURN) {
            $result = $result->unwrap();
        }

        return $result;
    }

    private function evalBlock(array $statements, Environment $env) : IObject
    {
        $result = new NullObject();
        $n = count($statements);

        for ($i = 0; $i<$n; $i++) {
            $res = $this->eval($statements[$i], $env);
            if ($i < $n-1) {
                $type_ = $res->getType();
                if ($type_ === ObjectType::RETURN || $type_ === ObjectType::ERROR) {
                    $result = $res;
                    break;
                }
            } else { // last statement evaluates to implicit return value
                $result = $res;
            }
        }

        return $result;
    }

}