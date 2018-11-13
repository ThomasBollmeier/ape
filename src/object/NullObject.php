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

    public function getType()
    {
        return ObjectType::NULL;
    }

    public function toString()
    {
        return "null";
    }
}