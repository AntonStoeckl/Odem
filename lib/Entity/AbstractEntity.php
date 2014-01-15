<?php

namespace Odem\Entity;

use Odem\Assert\Assertion;
use Odem\Assert\Property as PropertyAssertion;
use Odem\Assert\ProperyAssertionInterface;

/**
 * Class AbstractEntity
 *
 * @package Odem\Entity
 */
abstract class AbstractEntity
{
    const UNDEF = '__IS_NOT_DEFINED__';

    const PHP_MAX_STR_LEN = 2147483647;

    /** @var  ProperyAssertionInterface */
    protected $propertyAssertions;

    /**  @var array */
    protected $defaultMappings = array();

    /** @var array */
    protected $data = array();

    /**
     * The constructor.
     * Initializes default mappings.
     */
    public function __construct()
    {
        $this->addDefaultMappings();
    }

    /**
     * @param ProperyAssertionInterface $propertyAssertions
     * @return $this
     */
    public function setPropertyAssertions(ProperyAssertionInterface $propertyAssertions)
    {
        $this->propertyAssertions = $propertyAssertions;

        return $this;
    }

    /**
     * @return ProperyAssertionInterface
     */
    public function getPropertyAssertions()
    {
        if (empty($this->propertyAssertions)) {
            $this->propertyAssertions = new PropertyAssertion();
        }

        return $this->propertyAssertions;
    }

    /**
     * @param string $method
     * @param array $params
     * @return $this
     * @throws \BadMethodCallException
     * @throws \Assert\InvalidArgumentException
     */
    public function __call($method, array $params)
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

        $this->getPropertyAssertions()->assertValidPropertyDefinition(
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
            case 'is':
                $this->doGet($property);
                break;
            case 'add':
                $this->doAdd($property, $params[0]);
                break;
            default:
                throw new \BadMethodCallException("Undefined method called: [{$method}]");
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function getEntityName()
    {
        $entityClass = get_class($this);
        $parts = explode('\\', $entityClass);
        $entityName = array_shift($parts);

        return $entityName;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return $this
     */
    protected function doSet($property, $value)
    {
        $propertyMapping = $this->getMappingForProperty($property);
        $this->getPropertyAssertions()
            ->assertValueIsValidType($propertyMapping, $this->defaultMappings, $value);

        $this->data[$property] = $value;

        return $this;
    }

    /**
     * @param string $property
     * @param mixed $value
     * @return $this
     */
    protected function doAdd($property, $value)
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

        $this->getPropertyAssertions()
            ->assertValueIsType($value, $itemType);

        $this->data[$property][] = $value;

        return $this;
    }

    /**
     * @param string $property
     * @return mixed
     */
    protected function doGet($property)
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
    protected function getMappingForProperty($property, $plain = false)
    {
        $entityMapping = $this->getMapping();
        Assertion::keyExists($entityMapping, $property, "Property [{$property}] not defined in this entity");
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
    protected function getDefaultMappingForType($type)
    {
        Assertion::string($type, "Expected 'type' to be a string");
        Assertion::notEmpty($type, "Expected 'type' to be not empty");
        Assertion::keyExists($this->defaultMappings, $type, "No default mapping defined for type [{$type}] defined");

        return $this->defaultMappings[$type];
    }

    /**
     * @param string $type
     * @param array $mapping
     * @return $this
     * @throws \Assert\InvalidArgumentException
     */
    protected function addDefaultMapping($type, array $mapping)
    {
        Assertion::string($type, "Expected 'type' to be a string");
        Assertion::notEmpty($type, "Expected 'type' to be not empty");
        Assertion::keyExists($mapping, 'nullable', "Mapping has no field [nullable]");
        Assertion::keyNotExists($this->defaultMappings, $type, "Mapping for type [{$type}] already exists");

        $this->defaultMappings[$type] = $mapping;

        return $this;
    }

    /**
     * Add default mappings
     */
    protected function addDefaultMappings()
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
