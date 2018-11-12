<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 12.11.2018
 * Time: 22:06
 */

namespace tbollmeier\ape\interpreter;


use tbollmeier\ape\object\IObject;
use tbollmeier\parsian\output\Ast;

class Interpreter
{
    public function eval(Ast $apeProgramm, Environment $env) : IObject
    {
        return null;
    }

}