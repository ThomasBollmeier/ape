<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 19.11.2018
 * Time: 20:41
 */

namespace tbollmeier\ape\interpreter;

use tbollmeier\ape\object\Integer;
use PHPUnit\Framework\TestCase;
use tbollmeier\ape\object\ObjectType;

class EnvironmentTest extends TestCase
{
    public function testClone()
    {
        $global = new Environment();
        $global->setSymbol("currentId", new Integer(42));

        $local = new Environment($global);
        $local->setSymbol("x", new Integer(23));

        $copy = $local->clone();
        $global->setSymbol("currentId", new Integer(43));

        $value = $copy->getSymbol("x");
        $this->assertEquals(ObjectType::INTEGER, $value->getType());
        $this->assertEquals(23, $value->getInt());

        $value = $copy->getSymbol("currentId");
        $this->assertEquals(ObjectType::INTEGER, $value->getType());
        $this->assertEquals(42, $value->getInt());

    }
}
