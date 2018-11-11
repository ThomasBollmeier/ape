<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 11.11.2018
 * Time: 11:50
 */

namespace tbollmeier\ape\object;


interface IObject
{
    public function getType();
    public function toString();
}