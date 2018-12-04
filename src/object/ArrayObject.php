<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 04.12.2018
 * Time: 20:20
 */

namespace tbollmeier\ape\object;


class ArrayObject implements IObject
{
    private $elements;

    public function __construct($elements = [])
    {
        $this->elements = $elements;
    }

    public function addElement(IObject $element)
    {
        $this->elements[] = $element;
    }

    public function get(int $i) : IObject
    {
        if ($i >= 0 && $i < count($this->elements)) {
            return $this->elements[$i];
        } else {
            return NullObject::getInstance();
        }
    }

    public function getType()
    {
        return ObjectType::ARRAY;
    }

    public function toString()
    {
        $ret = "[";
        $first = true;

        foreach ($this->elements as $element) {
            if ($first) {
                $first = false;
            } else {
                $ret .= ", ";
            }
            $ret .= $element->toString();
        }

        $ret .= "]";

        return $ret;
    }

    public function copy(): IObject
    {
        $ret = new ArrayObject();

        foreach ($this->elements as $element) {
            $ret->addElement($element->copy());
        }

        return $ret;
    }
}