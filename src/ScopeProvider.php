<?php

namespace dice;

class ScopeProvider
{
    private static $name = __NAMESPACE__ . '\Scope';

    private static $config = [];

    public static function setClassName($name = null) {
        if ($name === null || empty($name))
            $name = __NAMESPACE__ . '\Scope';
        self::$name = $name;
    }

    public static function getClassName() {
        return self::$name;
    }

    public static function setConfig($config = null) {
        if ($config === null || empty($config))
            $config = [];
        self::$config = $config;
    }

    public static function getConfig() {
        return self::$config;
    }

    public static function getScope($config = []) {
        if ($config === null || empty($config))
            $config = [];
        if ($config instanceof self::$name)
            return $config;
        return new self::$name(array_merge(self::$config, $config));
    }
}
