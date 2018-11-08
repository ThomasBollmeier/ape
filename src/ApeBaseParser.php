<?php
/* This file has been generated by the Parsian parser generator 
 * (see http://github.com/thomasbollmeier/parsian)
 * 
 * DO NOT EDIT THIS FILE!
 */
namespace tbollmeier\ape;

use tbollmeier\parsian as parsian;
use tbollmeier\parsian\output\Ast;


class ApeBaseParser extends parsian\Parser
{
    public function __construct()
    {
        parent::__construct();

        $this->configLexer();
        $this->configGrammar();
    }

    private function configLexer()
    {

        $lexer = $this->getLexer();

        $lexer->addCommentType("--", "\n");

        $lexer->addStringType("\"", "#\"");

        $lexer->addSymbol("=", "EQ");
        $lexer->addSymbol(";", "SEMICOLON");
        $lexer->addSymbol("+", "PLUS");
        $lexer->addSymbol("-", "MINUS");
        $lexer->addSymbol("*", "ASTERISK");
        $lexer->addSymbol("/", "SLASH");
        $lexer->addSymbol("(", "LPAR");
        $lexer->addSymbol(")", "RPAR");
        $lexer->addSymbol("[", "LBRACKET");
        $lexer->addSymbol("]", "RBRACKET");
        $lexer->addSymbol("{", "LBRACE");
        $lexer->addSymbol("}", "RBRACE");
        $lexer->addSymbol(",", "COMMA");
        $lexer->addSymbol(":", "COLON");

        $lexer->addTerminal("/[a-zA-Z_][a-zA-Z0-9_]*/", "ID");
        $lexer->addTerminal("/[1-9][0-9]*/", "INT");

        $lexer->addKeyword("let");
        $lexer->addKeyword("return");
        $lexer->addKeyword("fn");

    }

    private function configGrammar()
    {

        $grammar = $this->getGrammar();

        $grammar->rule("ape",
            $grammar->oneOrMore($grammar->ruleRef("stmt")),
            true);
        $grammar->rule("stmt",
            $this->stmt(),
            false);

        $grammar->setCustomRuleAst("stmt", function (Ast $ast) {
            $child = $ast->getChildren()[0];
            $child->clearId();
            return $child;
        });

        $grammar->rule("let_stmt",
            $this->let_stmt(),
            false);

        $grammar->setCustomRuleAst("let_stmt", function (Ast $ast) {
            $res = new Ast("let_stmt", "");
            $local_1 = new Ast("name", $ast->getChildrenById("name")[0]->getText());
            $res->addChild($local_1);
            $local_2 = new Ast("value", "");
            $local_3 = $ast->getChildrenById("value")[0];
            $local_3->clearId();
            $local_2->addChild($local_3);
            $res->addChild($local_2);
            return $res;
        });

        $grammar->rule("return_stmt",
            $this->return_stmt(),
            false);

        $grammar->setCustomRuleAst("return_stmt", function (Ast $ast) {
            $res = new Ast("return_stmt", "");
            $local_1 = new Ast("value", "");
            $local_2 = $ast->getChildrenById("value")[0];
            $local_2->clearId();
            $local_1->addChild($local_2);
            $res->addChild($local_1);
            return $res;
        });

        $grammar->rule("expr_stmt",
            $this->expr_stmt(),
            false);

        $grammar->setCustomRuleAst("expr_stmt", function (Ast $ast) {
            $res = new Ast("expr_stmt", "");
            $local_1 = $ast->getChildrenById("ex")[0];
            $local_1->clearId();
            $res->addChild($local_1);
            return $res;
        });

        $grammar->rule("expr",
            $this->expr(),
            false);
        $grammar->rule("prod",
            $this->prod(),
            false);
        $grammar->rule("factor",
            $this->factor(),
            false);

        $grammar->setCustomRuleAst("factor", function (Ast $ast) {
            $child = $ast->getChildren()[0];
            $child->clearId();
            return $child;
        });

        $grammar->rule("func_expr",
            $this->func_expr(),
            false);

        $grammar->setCustomRuleAst("func_expr", function (Ast $ast) {
            $res = new Ast("func_expr", "");
            $local_1 = new Ast("parameters", "");
            foreach ($ast->getChildrenById("p") as $local_2) {
                $local_2->clearId();
                $local_1->addChild($local_2);
            }
            $res->addChild($local_1);
            $local_3 = new Ast("body", "");
            foreach ($ast->getChildrenById("st") as $local_4) {
                $local_4->clearId();
                $local_3->addChild($local_4);
            }
            $res->addChild($local_3);
            return $res;
        });

        $grammar->rule("array_literal",
            $this->array_literal(),
            false);

        $grammar->setCustomRuleAst("array_literal", function (Ast $ast) {
            $res = new Ast("array", "");
            foreach ($ast->getChildrenById("el") as $local_1) {
                $local_1->clearId();
                $res->addChild($local_1);
            }
            return $res;
        });

        $grammar->rule("map_literal",
            $this->map_literal(),
            false);

        $grammar->setCustomRuleAst("map_literal", function (Ast $ast) {
            $res = new Ast("map", "");
            foreach ($ast->getChildrenByName("entry") as $local_1) {
                $local_1->clearId();
                $res->addChild($local_1);
            }
            return $res;
        });

        $grammar->rule("entry",
            $this->entry(),
            false);

        $grammar->setCustomRuleAst("entry", function (Ast $ast) {
            $res = new Ast("entry", "");
            $local_1 = $ast->getChildrenById("key")[0];
            $local_1->clearId();
            $res->addChild($local_1);
            $local_2 = $ast->getChildrenById("value")[0];
            $local_2->clearId();
            $res->addChild($local_2);
            return $res;
        });

        $grammar->rule("idx_access_or_call",
            $this->idx_access_or_call(),
            false);
        $grammar->rule("target",
            $this->target(),
            false);

        $grammar->setCustomRuleAst("target", function (Ast $ast) {
            $child = $ast->getChildren()[0];
            $child->clearId();
            return $child;
        });

        $grammar->rule("group",
            $this->group(),
            false);

        $grammar->setCustomRuleAst("group", function (Ast $ast) {
            $child = $ast->getChildrenById("ex")[0];
            $child->clearId();
            return $child;
        });


    }

    private function alt_1()
    {
        $grammar = $this->getGrammar();

        return $grammar->alt()
            ->add($grammar->term("PLUS"))
            ->add($grammar->term("MINUS"));
    }

    private function alt_2()
    {
        $grammar = $this->getGrammar();

        return $grammar->alt()
            ->add($grammar->term("ASTERISK"))
            ->add($grammar->term("SLASH"));
    }

    private function alt_3()
    {
        $grammar = $this->getGrammar();

        return $grammar->alt()
            ->add($this->seq_9())
            ->add($this->seq_10());
    }

    private function factor()
    {
        $grammar = $this->getGrammar();

        return $grammar->alt()
            ->add($grammar->ruleRef("idx_access_or_call"))
            ->add($grammar->ruleRef("array_literal"))
            ->add($grammar->ruleRef("map_literal"))
            ->add($grammar->ruleRef("func_expr"))
            ->add($grammar->term("ID"))
            ->add($grammar->term("INT"))
            ->add($grammar->term("STRING"))
            ->add($grammar->ruleRef("group"));
    }

    private function stmt()
    {
        $grammar = $this->getGrammar();

        return $grammar->alt()
            ->add($grammar->ruleRef("let_stmt"))
            ->add($grammar->ruleRef("return_stmt"))
            ->add($grammar->ruleRef("expr_stmt"));
    }

    private function target()
    {
        $grammar = $this->getGrammar();

        return $grammar->alt()
            ->add($grammar->ruleRef("array_literal"))
            ->add($grammar->ruleRef("map_literal"))
            ->add($grammar->ruleRef("func_expr"))
            ->add($grammar->term("ID"));
    }


    private function array_literal()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("LBRACKET"))
            ->add($grammar->opt($this->seq_5()))
            ->add($grammar->term("RBRACKET"));
    }

    private function entry()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->ruleRef("expr", "key"))
            ->add($grammar->term("COLON"))
            ->add($grammar->ruleRef("expr", "value"));
    }

    private function expr()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->ruleRef("prod"))
            ->add($grammar->many($this->seq_1()));
    }

    private function expr_stmt()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->ruleRef("expr", "ex"))
            ->add($grammar->term("SEMICOLON"));
    }

    private function func_expr()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("FN"))
            ->add($grammar->term("LPAR"))
            ->add($grammar->opt($this->seq_3()))
            ->add($grammar->term("RPAR"))
            ->add($grammar->term("LBRACE"))
            ->add($grammar->many($grammar->ruleRef("stmt", "st")))
            ->add($grammar->term("RBRACE"));
    }

    private function group()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("LPAR"))
            ->add($grammar->ruleRef("expr", "ex"))
            ->add($grammar->term("RPAR"));
    }

    private function idx_access_or_call()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->ruleRef("target", "tgt"))
            ->add($grammar->oneOrMore($this->alt_3()));
    }

    private function let_stmt()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("LET"))
            ->add($grammar->term("ID", "name"))
            ->add($grammar->term("EQ"))
            ->add($grammar->ruleRef("expr", "value"))
            ->add($grammar->term("SEMICOLON"));
    }

    private function map_literal()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("LBRACE"))
            ->add($grammar->opt($this->seq_7()))
            ->add($grammar->term("RBRACE"));
    }

    private function prod()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->ruleRef("factor"))
            ->add($grammar->many($this->seq_2()));
    }

    private function return_stmt()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("RETURN"))
            ->add($grammar->ruleRef("expr", "value"))
            ->add($grammar->term("SEMICOLON"));
    }

    private function seq_1()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($this->alt_1())
            ->add($grammar->ruleRef("prod"));
    }

    private function seq_10()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("LPAR"))
            ->add($grammar->opt($this->seq_11()))
            ->add($grammar->term("RPAR"));
    }

    private function seq_11()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->ruleRef("expr", "arg"))
            ->add($grammar->many($this->seq_12()));
    }

    private function seq_12()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("COMMA"))
            ->add($grammar->ruleRef("expr", "arg"));
    }

    private function seq_2()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($this->alt_2())
            ->add($grammar->ruleRef("factor"));
    }

    private function seq_3()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->ruleRef("expr", "p"))
            ->add($grammar->many($this->seq_4()));
    }

    private function seq_4()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("COMMA"))
            ->add($grammar->ruleRef("expr", "p"));
    }

    private function seq_5()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->ruleRef("expr", "el"))
            ->add($grammar->many($this->seq_6()));
    }

    private function seq_6()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("COMMA"))
            ->add($grammar->ruleRef("expr", "el"));
    }

    private function seq_7()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->ruleRef("entry"))
            ->add($grammar->many($this->seq_8()));
    }

    private function seq_8()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("COMMA"))
            ->add($grammar->ruleRef("entry"));
    }

    private function seq_9()
    {
        $grammar = $this->getGrammar();

        return $grammar->seq()
            ->add($grammar->term("LBRACKET"))
            ->add($grammar->ruleRef("expr", "idx"))
            ->add($grammar->term("RBRACKET"));
    }


}
