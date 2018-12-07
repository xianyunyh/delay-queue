<?php

namespace DelayQueue;

class Container extends \Pimple\Container
{
    public static $instance;

    public static function getInstance()
    {
        if (!self::$instance instanceof Container) {
            self::$instance = new Container();
        }
        return self::$instance;
    }
}