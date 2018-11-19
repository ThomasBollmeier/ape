<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 12.11.2018
 * Time: 22:13
 */

namespace tbollmeier\ape\interpreter;


use tbollmeier\ape\object\IObject;

class Environment
{
    private $outer;
    private $symbols;

    public function __construct(Environment $outer = null)
    {
        $this->outer = $outer;
        $this->symbols = [];
    }

    public function setSymbol(string $name, IObject $value): void
    {
        $this->symbols[$name] = $value;
    }
    
    public  function getSymbol(string $name) 
    {
        if (array_key_exists($name, $this->symbols)) {
            return $this->symbols[$name];
        } else if ($this->outer !== null) {
            return $this->outer->getSymbol($name);
        } 
        
        return null;
    }

    public function clone() : Environment
    {
        $ret = new Environment();
        $ret->symbols = $this->getAllSymbols();

        return $ret;
    }

    private function getAllSymbols()
    {
        $ret = [];

        $envs = [];
        $env = $this;
        while ($env !== null) {
            $envs = array_merge([$env], $envs);
            $env = $env->outer;
        }

        foreach ($envs as $env) {
            foreach ($env->symbols as $name => $value) {
                $ret[$name] = $value;
            }
        }

        return $ret;
    }

}