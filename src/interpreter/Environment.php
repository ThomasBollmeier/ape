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

}