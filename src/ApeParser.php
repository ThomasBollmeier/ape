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

    }

}