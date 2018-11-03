<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 02.11.2018
 * Time: 22:27
 */

namespace tbollmeier\ape;
use tbollmeier\parsian\output\Ast;


class ApeParser extends ApeBaseParser
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

        $g->setCustomRuleAst("expr", [$this, "transBinOp"]);
        $g->setCustomRuleAst("prod", [$this, "transBinOp"]);
        $g->setCustomRuleAst("factor", [$this, "transFactor"]);

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

    public function transFactor(Ast $ast) {

        $base = $ast->getChildrenById("base")[0];
        $idxs = $ast->getChildrenById("idx");

        if (count($idxs) == 0) {
            $base->clearId();
            return $base;
        }

        return $ast;
    }

}