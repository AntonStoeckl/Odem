<?php

namespace Odem\Entity;

use Odem\Assert\Assertion;
use Odem\Assert\Property as PropertyAssertion;
use Odem\Assert\PropertyAssertionInterface;
use Odem\Entity\DefaultMappings\AbstractDefaultMappings;

/**
 * Class AbstractEntity
 *
 * @package Odem\Entity
 */
abstract class AbstractEntity
{
    /**
     * @var  PropertyAssertionInterface
     */
    protected $propertyAssertions;

    /**
     * @var AbstractDefaultMappings
     */
    protected $defaultMappings;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * The constructor.
     * Receives dependencies via injection.
     *
     * @param AbstractDefaultMappings $defaultMappings
     */
    public function __construct(AbstractDefaultMappings $defaultMappings)
    {
        $this->defaultMappings = $defaultMappings;
    }

    /**
     * @param PropertyAssertionInterface $propertyAssertions
     * @return $this
     */
    public function setPropertyAssertions(PropertyAssertionInterface $propertyAssertions)
    {
        $this->propertyAssertions = $propertyAssertions;

        return $this;
    }

    /**
     * @return PropertyAssertionInterface
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
            $this->defaultMappings->getAll(),
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
            ->assertValueIsValidType($propertyMapping, $this->defaultMappings->getAll(), $value);

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

        $mapping = array_merge($this->defaultMappings->getForType($propertyType), $propertyMapping);

        return $mapping;
    }

    /**
     * @return array
     */
    abstract public function getMapping();
}
