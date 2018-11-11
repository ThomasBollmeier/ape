<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 11.11.2018
 * Time: 11:53
 */

namespace tbollmeier\ape\object;


class Integer implements IObject
{

    public function getType()
    {
        return ObjectType::INTEGER;
    }

    public function toString()
    {
        // TODO: Implement toString() method.
    }
}