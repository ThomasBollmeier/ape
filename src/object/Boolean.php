<?php
/**
 * Created by PhpStorm.
 * User: thoma
 * Date: 19.11.2018
 * Time: 21:21
 */

namespace tbollmeier\ape\object;


class Boolean implements IObject
{
    private static $true = null;
    private static $false = null;
    private $value;

    public static function toBoolean(bool $value)
    {
        return $value ? self::getTrue(): self::getFalse();
    }

    public static function getTrue()
    {
        if (static::$true == null) {
            static::$true = new Boolean(true);
        }

        return static::$true;
    }

    public static function getFalse()
    {
        if (static::$false == null) {
            static::$false = new Boolean(false);
        }

        return static::$false;
    }

    private function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function getBool()
    {
        return $this->value;
    }

    public function getType()
    {
        return ObjectType::BOOLEAN;
    }

    public function toString()
    {
        return $this->value ? "true" : "false";
    }

    public function copy(): IObject
    {
        return new Boolean($this->value);
    }
}