<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 12.11.2018
 * Time: 22:06
 */

namespace tbollmeier\ape\interpreter;


use function Couchbase\defaultDecoder;
use tbollmeier\ape\object\ArrayObject;
use tbollmeier\ape\object\Boolean;
use tbollmeier\ape\object\ErrorObject;
use tbollmeier\ape\object\FuncObject;
use tbollmeier\ape\object\Integer;
use tbollmeier\ape\object\IObject;
use tbollmeier\ape\object\NullObject;
use tbollmeier\ape\object\ObjectType;
use tbollmeier\ape\object\ReturnObject;
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
            case "let_stmt":
                return $this->evalLetStmt($ast, $env);
            case "expr_stmt":
                return $this->evalExprStmt($ast, $env);
            case "if":
                return $this->evalIfExpr($ast, $env);
            case "func_expr":
                return $this->evalFuncExpr($ast, $env);
            case "call":
                return $this->evalCall($ast, $env);
            case "return_stmt":
                return $this->evalReturnStmt($ast, $env);
            case "negative":
                return $this->evalNegative($ast, $env);
            case "not":
                return $this->evalNot($ast, $env);
            case "binop":
                return $this->evalBinaryOp($ast, $env);
            case "or":
                return $this->evalDisjunction($ast, $env);
            case "and":
                return $this->evalConjunction($ast, $env);
            case "logic_relation":
                return $this->evalLogicalRel($ast, $env);
            case "identifier":
                return $this->evalIdentifier($ast, $env);
            case "array":
                return $this->evalArray($ast, $env);
            case "element_access":
                return $this->evalElementAccess($ast, $env);
            case "integer":
                return $this->evalInteger($ast);
            case "null":
                return NullObject::getInstance();
            case "true":
                return Boolean::getTrue();
            case "false":
                return Boolean::getFalse();
        }

        return NullObject::getInstance();
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
        $result = NullObject::getInstance();
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

    private function evalLetStmt(Ast $ast, Environment $env) : IObject
    {
        list($name, $value) = $ast->getChildren();

        $identifier = $name->getText();
        $obj = $this->eval($value->getChildren()[0], $env);
        $env->setSymbol($identifier, $obj);

        if ($obj->getType() == ObjectType::FUNCTION) {
            // Set function name in function environment so that
            // recursive calls are enabled
            $obj->setFuncName($identifier);
        }

        return NullObject::getInstance();
    }

    private function evalExprStmt(Ast $ast, Environment $env) : IObject
    {
        $expr = $ast->getChildren()[0];
        return $this->eval($expr, $env);
    }

    private function evalIfExpr(Ast $ast, Environment $env) : IObject
    {
        $children = $ast->getChildren();

        list($condition, $consequent, $alternative) = $children;

        $condResult = $this->getTruthy($this->eval($condition->getChildren()[0], $env));
        if ($condResult->getBool()) {
            $stmts = $consequent->getChildren();
            return $this->evalBlock($stmts, $env);
        } else {
            $stmts = $alternative->getChildren();
            if (count($stmts) > 0) {
                return $this->evalBlock($stmts, $env);
            } else {
                return NullObject::getInstance();
            }
        }

    }

    private function evalFuncExpr(Ast $ast, Environment $env)  : IObject
    {
        list($parameters, $body) = $ast->getChildren();
        $params = array_map(function ($ast) { return $ast->getText(); },
            $parameters->getChildren());

        return new FuncObject($params, $body, $env->clone());
    }

    private function evalCall(Ast $ast, Environment $env) : IObject
    {
        list($callee, $arguments) = $ast->getChildren();

        $fn = $this->eval($callee->getChildren()[0], $env);
        if ($fn->getType() != ObjectType::FUNCTION) {
            return new ErrorObject($fn->toString() . " is not a function");
        }

        $closure = new Environment($fn->getEnv());

        $args = $arguments->getChildren();
        $params = $fn->getParams();
        $numArgs = count($args);
        if ($numArgs != count($params)) {
            return new ErrorObject("#arguments does not match #parameters");
        }

        for ($i=0; $i<$numArgs; $i++) {
            $value = $this->eval($args[$i], $env);
            if ($value->getType() == ObjectType::ERROR) {
                return $value;
            }
            $closure->setSymbol($params[$i], $value);
        }

        $stmts = $fn->getBody()->getChildren();
        $result = $this->evalBlock($stmts, $closure);
        if ($result->getType() === ObjectType::RETURN) {
            $result = $result->unwrap();
        }

        return $result;
    }

    private function evalReturnStmt(Ast $ast, Environment $env) : IObject
    {
        $valueNode = $ast->getChildren()[0];
        $exprNode = $valueNode->getChildren()[0];
        $value = $this->eval($exprNode, $env);

        if ($value->getType() == ObjectType::ERROR) {
            return $value;
        }

        return new ReturnObject($value);
    }

    private function evalIdentifier(Ast $ast, Environment $env) : IObject
    {
        $id = $ast->getText();
        return $env->getSymbol($id);
    }

    private function evalArray(Ast $ast, Environment $env) : IObject
    {
        $ret = new ArrayObject();

        $elements = $ast->getChildren();
        foreach ($elements as $element) {
            $ret->addElement($this->eval($element, $env));
        }

        return $ret;
    }

    private function evalElementAccess(Ast $ast, Environment $env) : IObject
    {
        list ($container, $index) = $ast->getChildren();
        $container = $container->getChildren()[0];
        $index = $index->getChildren()[0];

        $containerObj = $this->eval($container, $env);
        $containerType = $containerObj->getType();
        if ( $containerType != ObjectType::ARRAY &&
             $containerType != ObjectType::MAP) {
            return new ErrorObject("Element access only supported for arrays and maps");
        }

        $indexObj = $this->eval($index, $env);

        switch ($containerType) {
            case ObjectType::ARRAY:
                if ($indexObj->getType() == ObjectType::INTEGER) {
                    return $containerObj->get($indexObj->getInt());
                } else {
                    return new ErrorObject("Array index must be integer");
                }
            default:
                return new ErrorObject("not indexable");
        }

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
                    return new Integer($left->getInt() + $right->getInt());
                } else {
                    return new ErrorObject("unsupported operand types for $op");
                }
                break;
            case "-":
                if ($left->getType() == ObjectType::INTEGER &&
                    $right->getType() == ObjectType::INTEGER) {
                    return new Integer($left->getInt() - $right->getInt());
                } else {
                    return new ErrorObject("unsupported operand types for $op");
                }
                break;
            case "*":
                if ($left->getType() == ObjectType::INTEGER &&
                    $right->getType() == ObjectType::INTEGER) {
                    return new Integer($left->getInt() * $right->getInt());
                } else {
                    return new ErrorObject("unsupported operand types for $op");
                }
                break;
            case "/":
                if ($left->getType() == ObjectType::INTEGER &&
                    $right->getType() == ObjectType::INTEGER) {
                    return new Integer($left->getInt() / $right->getInt());
                } else {
                    return new ErrorObject("unsupported operand types for $op");
                }
                break;
            default:
                return new ErrorObject("unknown operator '$op'");
        }

    }

    private function evalDisjunction(Ast $ast, Environment $env) : IObject
    {
        $children = $ast->getChildren();

        foreach ($children as $child) {
            $expr = $this->eval($child, $env);
            if (!$expr->getType() == ObjectType::BOOLEAN) {
                return new ErrorObject("Disjunction requires boolean operands");
            }
            if ($expr->getBool()) {
                return Boolean::getTrue();
            }
        }

        return Boolean::getFalse();
    }

    private function evalConjunction(Ast $ast, Environment $env) : IObject
    {
        $children = $ast->getChildren();

        foreach ($children as $child) {
            $expr = $this->eval($child, $env);
            if (!$expr->getType() == ObjectType::BOOLEAN) {
                return new ErrorObject("Conjunction requires boolean operands");
            }
            if (!$expr->getBool()) {
                return Boolean::getFalse();
            }
        }

        return Boolean::getTrue();
    }

    private function evalLogicalRel(Ast $ast, Environment $env) : IObject
    {
        $op = $ast->getAttr("operator");
        list($leftNode, $rightNode) = $ast->getChildren();
        $left = $this->eval($leftNode, $env);
        $right = $this->eval($rightNode, $env);

        // Currently only support for integer comparisons:
        if ($left->getType() != ObjectType::INTEGER ||
            $right->getType() != ObjectType::INTEGER) {
            return new ErrorObject("Only integers supported in comparisons");
        }

        $lval = $left->getInt();
        $rval = $right->getInt();

        switch ($op) {
            case "==":
                return Boolean::toBoolean($lval == $rval);
            case "!=":
                return Boolean::toBoolean($lval != $rval);
            case ">":
                return Boolean::toBoolean($lval > $rval);
            case "<":
                return Boolean::toBoolean($lval < $rval);
        }

        return new ErrorObject("Unsupported logical operator");

    }

    private function evalNegative(Ast $ast, Environment $env)
    {
        $child = $ast->getChildren()[0];
        $value = $this->eval($child, $env);

        if ($value->getType() != ObjectType::INTEGER) {
            return new ErrorObject("Unary operator '-' can only be applied to integers");
        }

        return new Integer(-$value->getInt());
    }

    private function evalNot(Ast $ast, Environment $env)
    {
        $child = $ast->getChildren()[0];
        $value = $this->getTruthy($this->eval($child, $env));

        return !$value->getBool() ? Boolean::getTrue() : Boolean::getFalse();
    }

    private function getTruthy(IObject $value) : Boolean
    {
        switch ($value->getType()) {
            case ObjectType::BOOLEAN:
                return $value;
            case ObjectType::NULL:
                return Boolean::getFalse();
            default:
                return Boolean::getTrue();
        }
    }

}