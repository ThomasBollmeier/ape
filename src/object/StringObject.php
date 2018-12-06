<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 06.12.2018
 * Time: 22:21
 */

namespace tbollmeier\ape\object;


class StringObject implements IObject
{
    private $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function concat(StringObject $other) : StringObject
    {
        return new StringObject($this->value . $other->value);
    }

    public function getType()
    {
        return ObjectType::STRING;
    }

    public function toString()
    {
        return "\"$this->value\"";
    }

    public function copy(): IObject
    {
        return new StringObject($this->value);
    }
}