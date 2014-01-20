<?php

namespace Odem\Assert;

/**
 * Interface PropertyAssertionInterface
 *
 * @package Odem\Assert
 */
interface PropertyAssertionInterface
{
    /**
     * Asserts that a value is of a distinct type.
     *
     * @param mixed $value
     * @param string $type
     */
    public function assertValueIsType($value, $type);

    /**
     * Asserts that a value is of valid type according to propertyMapping.
     *
     * @param array $propertyMapping
     * @param array $defaultMappings
     * @param mixed $value
     */
    public function assertValueIsValidType(array $propertyMapping, array $defaultMappings, $value);

    /**
     * Assert that property has a valid definition in entity
     *
     * @param array  $mapping
     * @param array  $defaultMappings
     * @param string $property
     * @param string $entityName
     */
    public function assertValidPropertyDefinition(
        array $mapping,
        array $defaultMappings,
        $property,
        $entityName
    );

    /**
     * Asserts that a type is defined in defaultMappings.
     *
     * @param array  $defaultMappings
     * @param string $type
     */
    public function assertKnownPropertyType(array $defaultMappings, $type);
}
