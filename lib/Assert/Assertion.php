<?php

namespace Odem\Assert;

use Assert\Assertion as BaseAssertion;

class Assertion extends BaseAssertion
{
    const INVALID_ARRAY_COUNT = 1000;
    const INVALID_KEY_NOT_EXISTS = 1001;
    const INVALID_METHOD_EXISTS = 1002;

    /**
     * Assert that value array has count elements.
     *
     * @param mixed  $value
     * @param int    $count
     * @param string $message
     * @param string $propertyPath
     * @return void
     * @throws \Assert\AssertionFailedException
     */
    public static function count($value, $count, $message = null, $propertyPath = null)
    {
        static::isArray($value);
        static::integerish($count);

        if (count($value) !== $count) {
            throw static::createException($message, static::INVALID_ARRAY_COUNT, $propertyPath);
        }
    }

    /**
     * Assert that key not already exists in array
     *
     * @param mixed $value
     * @param string|integer $key
     * @param string $message
     * @param string $propertyPath
     * @return void
     * @throws \Assert\AssertionFailedException
     */
    public static function keyNotExists($value, $key, $message = null, $propertyPath = null)
    {
        static::isArray($value);

        if (array_key_exists($key, $value)) {
            throw static::createException($message, static::INVALID_KEY_NOT_EXISTS, $propertyPath);
        }
    }

    /**
     * Assert that method exists in object
     *
     * @param object $object
     * @param string $method
     * @param null $message
     * @param null $propertyPath
     * @throws
     */
    public static function methodExists($object, $method, $message = null, $propertyPath = null)
    {
        if (! method_exists($object, $method)) {
            throw static::createException($message, static::INVALID_METHOD_EXISTS, $propertyPath);
        }
    }
}
