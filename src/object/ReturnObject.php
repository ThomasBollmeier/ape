<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 13.11.2018
 * Time: 07:44
 */

namespace tbollmeier\ape\object;


class ReturnObject implements IObject
{
    private $object;

    public function __construct(IObject $object)
    {
        $this->object = $object;
    }

    public function unwrap()
    {
        return $this->object;
    }

    public function getType()
    {
        return ObjectType::RETURN;
    }

    public function toString()
    {
        return "Return <" . $this->object->toString(). ">";
    }
}