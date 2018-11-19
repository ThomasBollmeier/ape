<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 13.11.2018
 * Time: 07:50
 */

namespace tbollmeier\ape\object;


class NullObject implements IObject
{
    private static $single = null;

    public static function getInstance() : NullObject
    {
        if (static::$single == null) {
            static::$single = new NullObject();
        }

        return static::$single;
    }

    private function __construct()
    {
    }

    public function getType()
    {
        return ObjectType::NULL;
    }

    public function toString()
    {
        return "null";
    }
}