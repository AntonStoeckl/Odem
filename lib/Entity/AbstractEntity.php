<?php

namespace Odem\Entity;

use Odem\Assert\Assertion;
use Odem\Assert\Property as PropertyAssertion;

/**
 * Class AbstractEntity
 *
 * @package Odem\Entity
 */
abstract class AbstractEntity
{
    const UNDEF = '__IS_NOT_DEFINED__';
    const PHP_MAX_STR_LEN = 2147483647;

    /**
     * @var array
     */
    protected $defaultMappings = array();

    /**
     * @var array
     */
    protected $data = array();

    /**
     * The constructor.
     * Initializes default mappings.
     */
    protected function __construct()
    {
        $this->addDefaultMappings();
    }

    /**
     * @param string $method
     * @param array $params
     * @return $this
     * @throws \BadMethodCallException
     * @throws \Assert\InvalidArgumentException
     */
    final public function __call($method, array $params)
    {
        $tokens = preg_split('/(?=[A-Z])/', $method);

        if (count($tokens) < 2) {
            throw new \BadMethodCallException("Invalid method called: [{$method}]");
        }

        Assertion::count($params, 1, "Invalid number of params, expected 1, got " . count($params));

        $action = array_shift($tokens);
        $property = implode('', $tokens);

        Assertion::string($property, "Expected 'property' to be a string");
        Assertion::notEmpty($property, "Expected 'property' to be a not empty string");

        PropertyAssertion::assertValidPropertyDefinition(
            $this->defaultMappings,
            $this->getMapping(),
            $property,
            $this->getEntityName()
        );

        switch ($action) {
            case 'set':
                $this->doSet($property, $params[0]);
                break;
            case 'get':
                $this->doGet($property);
                break;
            case 'add':
                $this->doAdd($property, $params[0]);
                break;
            case 'is':
                $this->doIs($property);
                break;
            default:
                throw new \BadMethodCallException("Undefined method called: [{$method}]");
        }

        return $this;
    }

    /**
     * @return string
     */
    final protected function getEntityName()
    {
        $entityClass = get_class($this);
        $entityName = array_shift(explode('\\', $entityClass));

        return $entityName;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return $this
     */
    final protected function doSet($property, $value)
    {
        $propertyMapping = $this->getMappingForProperty($property);
        PropertyAssertion::assertValueIsValidType($propertyMapping, $this->defaultMappings, $value);

        $this->data[$property] = $value;

        return $this;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return $this
     */
    final protected function doAdd($property, $value)
    {
        $mapping = $this->getMapping();
        $type = $mapping[$property]['type'];
        Assertion::same(
            'array',
            $type,
            "Adder function can only be called on properties of type array, "
            . "but property [{$property}] is defined as type [{$type}]"
        );

        $itemType = $mapping[$property]['itemType'];

        PropertyAssertion::assertValueIsType($value, $itemType);

        $this->data[$property][] = $value;

        return $this;
    }

    /**
     * @param string $property
     * @return mixed
     */
    final protected function doGet($property)
    {
        if (isset($this->data[$property]) || array_key_exists($property, $this->data)) {
            return $this->data[$property];
        }

        return null;
    }

    /**
     * @param string $property
     * @return bool|null
     */
    final protected function doIs($property)
    {
        if (isset($this->data[$property]) || array_key_exists($property, $this->data)) {
            return $this->data[$property];
        }

        return null;
    }

    /**
     * @param string $property
     * @param bool $plain
     * @return array
     * @throws \Assert\InvalidArgumentException
     */
    final protected function getMappingForProperty($property, $plain = false)
    {
        $entityMapping = $this->getMapping();
        Assertion::keyExists($property, $entityMapping, "Property [{$property}] not defined in this entity");
        $propertyMapping = $entityMapping[$property];
        $propertyType = $propertyMapping['type'];

        if ($plain === true) {
            return $propertyMapping;
        }

        $mapping = array_merge($this->getDefaultMappingForType($propertyType), $propertyMapping);

        return $mapping;
    }

    /**
     * @param string $type
     * @return array
     * @throws \Assert\InvalidArgumentException
     */
    final protected function getDefaultMappingForType($type)
    {
        Assertion::keyExists($type, $this->defaultMappings, "No default mapping defined for type [{$type}]");

        return $this->defaultMappings[$type];
    }

    /**
     * @param string $type
     * @param array $mapping
     * @return $this
     * @throws \Assert\InvalidArgumentException
     */
    final private function addDefaultMapping($type, array $mapping)
    {
        Assertion::string($type, "Expected 'type' to be a string");
        Assertion::notEmpty($type, "Expected 'type' to be not empty");
        Assertion::keyExists('nullable', $mapping, "Mapping has no field [nullable]");
        Assertion::keyNotExists($type, $this->defaultMappings, "Mapping for [{$type}] already exists");

        $this->defaultMappings[$type] = $mapping['mapping'];

        return $this;
    }

    /**
     * Add default mappings
     */
    final private function addDefaultMappings()
    {
        $this->addDefaultMapping(
            'integer',
            array('nullable' => true, 'min' => PHP_INT_MAX * -1, 'max' => PHP_INT_MAX, 'default' => null,)
        );

        $this->addDefaultMapping(
            'float',
            array('nullable' => true, 'min' => PHP_INT_MAX * -1, 'max' => PHP_INT_MAX, 'default' => null,)
        );

        $this->addDefaultMapping(
            'bool',
            array('nullable' => false, 'default' => false,)
        );

        $this->addDefaultMapping(
            'string',
            array('nullable' => true, 'minLen' => 0, 'maxLen' => self::PHP_MAX_STR_LEN, 'default' => null,)
        );

        $this->addDefaultMapping(
            'array',
            array('nullable' => false, 'default' => [], 'itemType' => 'string',)
        );

        $this->addDefaultMapping(
            'entity',
            array('nullable' => true, 'default' => null, 'class' => static::UNDEF)
        );
    }

    /**
     * @return array
     */
    abstract public function getMapping();
}
