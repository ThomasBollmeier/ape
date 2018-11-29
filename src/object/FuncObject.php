<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 29.11.2018
 * Time: 21:11
 */

namespace tbollmeier\ape\object;

use tbollmeier\ape\interpreter\Environment;
use tbollmeier\parsian\output\Ast;


class FuncObject implements IObject
{
    private $params;
    private $body;
    private $env;

    public function __construct($params, Ast $body, Environment $env)
    {
        $this->params = $params;
        $this->body = $body;
        $this->env = $env;
    }

    public function getType()
    {
        return ObjectType::FUNCTION;
    }

    public function toString()
    {
        return "fn (" . implode(",", $this->params) . ") {...}";
    }

    public function copy(): IObject
    {
        return new FuncObject($this->params, $this->body, $this->env);
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return Ast
     */
    public function getBody(): Ast
    {
        return $this->body;
    }

    /**
     * @return Environment
     */
    public function getEnv(): Environment
    {
        return $this->env;
    }

    public function setFuncName($name) {
        $this->env->setSymbol($name, $this);
    }
}