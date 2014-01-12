<?php

namespace Odem\Assert;

/**
 * Class Property
 * @package Odem\Assert
 */
class Property implements ProperyAssertionInterface
{
    /**
     * @param array $propertyMapping
     * @param array $defaultMappings
     * @param mixed $value
     */
    public static function assertValueIsValidType(array $propertyMapping, array $defaultMappings, $value)
    {
        $propertyType = $propertyMapping['type'];

        static::assertKnownPropertyType($defaultMappings, $propertyType);

        static::assertValueIsType($value, $propertyType);
    }

    /**
     * @param mixed $value
     * @param string $type
     * @throws \Assert\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public static function assertValueIsType($value, $type)
    {
        switch ($type) {
            case 'integer':
                Assertion::integerish($value, "Expected integer value, got: " . gettype($value));
                break;
            case 'float':
                Assertion::numeric($value, "Expected float value, got: " . gettype($value));
                break;
            case 'bool':
                Assertion::boolean($value, "Expected boolean value, got: " . gettype($value));
                break;
            case 'string':
                Assertion::string($value, "Expected string value, got: " . gettype($value));
                break;
            case 'array':
                Assertion::isArray($value, "Expected array value, got: " . gettype($value));
                break;
            case 'entity':
                Assertion::isInstanceOf(
                    $value,
                    'Odem\\Entity\\AbstractEntity',
                    "Expected value of type [entity], got: gettype($value)"
                );
                break;
            default:
                throw new \UnexpectedValueException("Assertion missing for property type: [{$type}]");
        }
    }

    /**
     * Assert that property has a valid definition in entity
     *
     * @param array  $mapping
     * @param array  $defaultMappings
     * @param string $property
     * @param string $entityName
     * @throws \Assert\InvalidArgumentException
     */
    public static function assertValidPropertyDefinition(array $mapping, array $defaultMappings, $property, $entityName)
    {
        Assertion::keyExists(
            $property,
            $mapping,
            "Property [{$property}] is not defined in entity [{$entityName}]"
        );

        Assertion::keyExists(
            'type',
            $mapping[$property],
            "Property [{$property}] has no field [type] in entity [{$entityName}]"
        );

        static::assertKnownPropertyType($defaultMappings, $mapping[$property]['type']);
    }

    /**
     * @param array  $defaultMappings
     * @param string $type
     * @throws \Assert\InvalidArgumentException
     */
    public static function assertKnownPropertyType(array $defaultMappings, $type)
    {
        Assertion::choice(
            $type,
            array_keys($defaultMappings),
            "Unknown property type: [{$type}]"
        );
    }
}
