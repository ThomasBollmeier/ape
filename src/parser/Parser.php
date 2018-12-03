<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 02.11.2018
 * Time: 22:27
 */

namespace tbollmeier\ape\parser;
use tbollmeier\parsian\output\Ast;


class Parser extends BaseParser
{

    public function __construct()
    {
        parent::__construct();

        $g = $this->getGrammar();

        $g->setCustomTermAst("ID", function (Ast $ast) {
            return new Ast("identifier", $ast->getText());
        });
        $g->setCustomTermAst("INT", function (Ast $ast) {
            return new Ast("integer", $ast->getText());
        });
        $g->setCustomTermAst("STRING", function (Ast $ast) {
           return new Ast("string", $ast->getText());
        });
        $g->setCustomTermAst("NULL", function (Ast $ast) {
            return new Ast("null");
        });
        $g->setCustomTermAst("TRUE", function (Ast $ast) {
            return new Ast("true");
        });
        $g->setCustomTermAst("FALSE", function (Ast $ast) {
            return new Ast("false");
        });

        $g->setCustomRuleAst("ape", [$this, "transApe"]);
        $g->setCustomRuleAst("disjunction", [$this, "transDisjunction"]);
        $g->setCustomRuleAst("conjunction", [$this, "transConjunction"]);
        $g->setCustomRuleAst("logic_rel", [$this, "transLogicRel"]);
        $g->setCustomRuleAst("sum", [$this, "transBinOp"]);
        $g->setCustomRuleAst("prod", [$this, "transBinOp"]);
        $g->setCustomRuleAst("factor", [$this, "transFactor"]);
        $g->setCustomRuleAst("idx_access_or_call", [$this, "transIdxAccessOrCall"]);
        $g->setCustomRuleAst("func_expr", [$this, "transFuncExpr"]);
        $g->setCustomRuleAst("if_expr", [$this, "transIfExpr"]);

    }

    public function transApe(Ast $ast)
    {
        $ret = new Ast("ape");
        $block = $ast->getChildren()[0];

        foreach ($block->getChildren() as $child) {
            $child->clearId();
            $ret->addChild($child);
        }

        return $ret;
    }

    public function transBinOp(Ast $ast) {

        $children = $ast->getChildren();
        $numChildren = count($children);

        if ($numChildren == 1) {
            return $children[0];
        }

        // Left associations

        $left = $children[0];
        $op = $children[1];
        $right = $children[2];
        $ret = new Ast("binop");
        $ret->setAttr("operator", $op->getText());
        $ret->addChild($left);
        $ret->addChild($right);

        $i = 3;
        while ($i < $numChildren) {
            $op = $children[$i];
            $right = $children[$i+1];
            $binop = new Ast("binop");
            $binop->setAttr("operator", $op->getText());
            $binop->addChild($ret);
            $binop->addChild($right);
            $ret = $binop;
            $i += 2;
        }

        return $ret;
    }

    public function transDisjunction(Ast $ast)
    {
        $ret = null;
        $conjunctions = $ast->getChildrenById("conj");
        if (count($conjunctions) == 1) {
            $ret = $conjunctions[0];
            $ret->clearId();
        } else {
            $ret = new Ast("or");
            foreach ($conjunctions as $conjunction) {
                $conjunction->clearId();
                $ret->addChild($conjunction);
            }
        }

        return $ret;
    }

    public function transConjunction(Ast $ast)
    {
        $ret = null;
        $conjunctions = $ast->getChildrenById("elem");
        if (count($conjunctions) == 1) {
            $ret = $conjunctions[0];
            $ret->clearId();
        } else {
            $ret = new Ast("and");
            foreach ($conjunctions as $conjunction) {
                $conjunction->clearId();
                $ret->addChild($conjunction);
            }
        }

        return $ret;
    }

    public function transLogicRel(Ast $ast)
    {
        $ret = null;
        $children = $ast->getChildren();
        switch (count($children)) {
            case 1:
                $ret = $children[0];
                break;
            default:
                $ret = new Ast("logic_relation");
                list($left, $op, $right) = $children;
                $ret->setAttr("operator", $op->getText());
                $ret->addChild($left);
                $ret->addChild($right);
        }

        return $ret;
    }

    public function transFactor(Ast $ast)
    {
        $ret = null;
        $children = $ast->getChildren();

        switch (count($children)) {
            case 1:
                $ret = $children[0];
                break;
            case 2:
                list($unaryOp, $value) = $children;
                switch ($unaryOp->getAttr("type")) {
                    case "NOT":
                        $ret = new Ast("not");
                        break;
                    default:
                        $ret = new Ast("negative");
                }
                $ret->addChild($value);
                break;
        }

        return $ret;
    }

    public function transIdxAccessOrCall(Ast $ast)
    {

        $ret = null;
        $target = null;
        $argList = [];

        $children = $ast->getChildren();

        foreach ($children as $child) {

            if ($child->hasAttr("type")) {
                $type_ = $child->getAttr("type");
                switch ($type_) {
                    case "LPAR":
                        $argList = [];
                        break;
                    case "RPAR":
                        $ret = $this->createCall($target, $argList);
                        $target = $ret;
                        $argList = [];
                        break;
                }
            }

            $id = $child->getId();

            switch ($id) {
                case "tgt":
                    $target = $child;
                    $target->clearId();
                    break;
                case "idx":
                    $child->clearId();
                    $ret = $this->createElemAccess($target, $child);
                    $target = $ret;
                    break;
                case "arg":
                    $child->clearId();
                    $argList[] = $child;
                    break;
            }
        }

        return $ret;

    }

    public function transFuncExpr(Ast $ast)
    {
        $ret = new Ast("func_expr");
        $params = $ast->getChildrenById("p");
        $block = $ast->getChildrenById("body")[0];

        $parameters = new Ast("parameters");
        $ret->addChild($parameters);
        foreach ($params as $param) {
            $param->clearId();
            $parameters->addChild($param);
        }

        $body = new Ast("body");
        $ret->addChild($body);
        foreach ($block->getChildren() as $stmt) {
            $stmt->clearId();
            $body->addChild($stmt);
        }

        return $ret;
    }

    public function transIfExpr(Ast $ast)
    {
        $ret = new Ast("if");
        $cond = $ast->getChildrenById("condition")[0];
        $cond->clearId();
        $condition = new Ast("condition");
        $ret->addChild($condition);
        $condition->addChild($cond);

        $consequent = new Ast("consequent");
        $ret->addChild($consequent);
        $cons = $ast->getChildrenById("consequent")[0];
        $cons->clearId();
        if ($cons->getName() == "block") {
            foreach ($cons->getChildren() as $stmt) {
                $consequent->addChild($stmt);
            }
        } else {
            $consequent->addChild($cons);
        }

        $alternative = new Ast("alternative");
        $ret->addChild($alternative);
        $alt = $ast->getChildrenById("alternative");
        if (count($alt) > 0) {
            $alt = $alt[0];
            $alt->clearId();
            if ($alt->getName() == "block") {
                foreach ($alt->getChildren() as $stmt) {
                    $alternative->addChild($stmt);
                }
            } else {
                $alternative->addChild($alt);
            }
        }

        return $ret;
    }

    private function createElemAccess($targetExpr, $indexExpr)
    {

        $ret = new Ast("element_access");

        $cont = new Ast("container");
        $ret->addChild($cont);
        $cont->addChild($targetExpr);

        $index = new Ast("index");
        $ret->addChild($index);
        $index->addChild($indexExpr);

        return $ret;
    }

    private function createCall($calleeExpr, $argExprs)
    {

        $ret = new Ast("call");

        $callee = new Ast("callee");
        $ret->addChild($callee);
        $callee->addChild($calleeExpr);

        $args = new Ast("arguments");
        $ret->addChild($args);

        foreach ($argExprs as $argExpr) {
            $args->addChild($argExpr);
        }

        return $ret;
    }

}