<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 18.11.2018
 * Time: 16:09
 */

namespace tbollmeier\ape\object;


class ErrorObject implements IObject
{
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getType()
    {
        return ObjectType::ERROR;
    }

    public function toString()
    {
        return "ERROR: " . $this->message;
    }
}