<?php

namespace Odem\Assert;

/**
 * Class Property
 *
 * @package Odem\Assert
 */
class Property implements PropertyAssertionInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \Assert\InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function assertValueIsType($value, $type)
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
     * {@inheritdoc}
     */
    public function assertValueIsValidType(array $propertyMapping, array $defaultMappings, $value)
    {
        $propertyType = $propertyMapping['type'];

        $this->assertKnownPropertyType($defaultMappings, $propertyType);

        $this->assertValueIsType($value, $propertyType);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Assert\InvalidArgumentException
     */
    public function assertValidPropertyDefinition(array $mapping, array $defaultMappings, $property, $entityName)
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

        $this->assertKnownPropertyType($defaultMappings, $mapping[$property]['type']);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Assert\InvalidArgumentException
     */
    public function assertKnownPropertyType(array $defaultMappings, $type)
    {
        Assertion::choice(
            $type,
            array_keys($defaultMappings),
            "Unknown property type: [{$type}]"
        );
    }
}
