<?php
namespace NinjaTestEmail\Utils;

trait Singleton {
    private static $instances = array();

    protected function __construct() {}

    private function __clone() {}

    public function __wakeup() {
        throw new \Exception('Cannot unserialize singleton');
    }

    public static function instance(...$args) {
        $class = static::class;

        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static(...$args);
        }

        return self::$instances[$class];
    }
}
