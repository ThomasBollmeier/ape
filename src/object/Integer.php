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
    private $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getInt(): int
    {
        return $this->value;
    }

    public function getType()
    {
        return ObjectType::INTEGER;
    }

    public function toString()
    {
        return sprintf("%d", $this->value);
    }

    public function copy(): IObject
    {
        return new Integer($this->value);
    }
}