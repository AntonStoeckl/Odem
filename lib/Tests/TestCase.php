<?php

namespace Odem\Tests;

/**
 * Class TestCase
 * @package Odem\Tests
 * @author Anton Stöckl <anton@stoeckl.com>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Asserts that a variable is equal to an attribute of an object.
     *
     * @param  mixed $expected
     * @param  string $actualAttributeName
     * @param  string $actualClassOrObject
     * @param  string $message
     * @param  float|int $delta
     * @param  integer $maxDepth
     * @param  boolean $canonicalize
     * @param  boolean $ignoreCase
     */
    public static function assertAttributeKeysEquals(
        $expected,
        $actualAttributeName,
        $actualClassOrObject,
        $message = '',
        $delta = 0,
        $maxDepth = 10,
        $canonicalize = false,
        $ignoreCase = false
    ) {
        self::assertEquals(
            $expected,
            array_keys(self::readAttribute($actualClassOrObject, $actualAttributeName)),
            $message,
            $delta,
            $maxDepth,
            $canonicalize,
            $ignoreCase
        );
    }
}
