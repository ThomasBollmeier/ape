<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 12.11.2018
 * Time: 22:06
 */

namespace tbollmeier\ape\interpreter;


use function Couchbase\defaultDecoder;
use tbollmeier\ape\object\ErrorObject;
use tbollmeier\ape\object\Integer;
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

    public function evalCode(string $program, Environment $env = null) : IObject
    {
        $ast = $this->parser->parseString($program);
        if ($ast !== false) {
            if ($env === null) {
                $env = new Environment();
            }
            return $this->eval($ast, $env);
        } else {
            return new ErrorObject($this->parser->error());
        }
    }


    private function eval(Ast $ast, Environment $env) : IObject
    {
        $name = $ast->getName();

        switch ($name) {
            case "ape":
                return $this->evalProgram($ast, $env);
            case "expr_stmt":
                return $this->evalExprStmt($ast, $env);
            case "binop":
                return $this->evalBinaryOp($ast, $env);
            case "integer":
                return $this->evalInteger($ast);
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

    private function evalExprStmt(Ast $ast, Environment $env) : IObject
    {
        $expr = $ast->getChildren()[0];
        return $this->eval($expr, $env);
    }

    private function evalInteger(Ast $ast) : IObject
    {
        $value = intval($ast->getText());
        return new Integer($value);
    }

    private function evalBinaryOp(Ast $ast, Environment $env) : IObject
    {
        $op = $ast->getAttr("operator");
        list($leftNode, $rightNode) = $ast->getChildren();
        $left = $this->eval($leftNode, $env);
        $right = $this->eval($rightNode, $env);

        switch ($op) {
            case "+":
                if ($left->getType() == ObjectType::INTEGER &&
                    $right->getType() == ObjectType::INTEGER) {
                    return new Integer($left->getValue() + $right->getValue());
                } else {
                    return new ErrorObject("unsupported operand types for $op");
                }
                break;
            default:
                return new ErrorObject("unknown operator '$op'");
        }

    }

}