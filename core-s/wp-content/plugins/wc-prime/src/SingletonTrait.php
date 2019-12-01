<?php

namespace WcPrime;

trait SingletonTrait
{
    private static $_instance;

    public static function getInstance()
    {
        if (is_null(static::$_instance)) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }
}
