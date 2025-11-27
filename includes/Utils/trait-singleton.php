<?php
namespace Ninja_KNP\Utils;


if (!trait_exists('Ninja_KNP\Utils\Ninja_KNP_Singleton')) {
trait Ninja_KNP_Singleton {
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



}
