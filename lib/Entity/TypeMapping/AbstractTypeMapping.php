<?php

namespace Odem\Entity\TypeMapping;

use Odem\Assert\Assertion;

/**
 * Class AbstractTypeMapping
 *
 * @package Odem\Entity\TypeMapping
 */
abstract class AbstractTypeMapping
{
    const PHP_MAX_STR_LEN = 2147483647;

    /**
     * @var array
     */
    protected $typeMapping = array();

    /**
     * @return array
     */
    public function getAll()
    {
        $this->initTypeMapping();

        return $this->typeMapping;
    }

    /**
     * @param string $type
     * @return array
     * @throws \Assert\InvalidArgumentException
     */
    public function getForType($type)
    {
        Assertion::string($type, "Expected 'type' to be a string");
        Assertion::notEmpty($type, "Expected 'type' to be not empty");

        $this->initTypeMapping();

        Assertion::keyExists($this->typeMapping, $type, "No default mapping defined for type [{$type}] defined");

        return $this->typeMapping[$type];
    }

    /**
     * @param string $type
     * @param array $mapping
     * @return $this
     * @throws \Assert\InvalidArgumentException
     */
    protected function addTypeMappingEntry($type, array $mapping)
    {
        Assertion::string($type, "Expected 'type' to be a string");
        Assertion::notEmpty($type, "Expected 'type' to be not empty");
        Assertion::keyExists($mapping, 'nullable', "Mapping has no field [nullable]");
        Assertion::keyNotExists($this->typeMapping, $type, "Mapping for type [{$type}] already exists");

        $this->typeMapping[$type] = $mapping;

        return $this;
    }

    /**
     * Initialize the type mapping.
     *
     * Uses @see AbstractDefaultMapping::addTypeMappingEntry()
     * for adding each default mapping so that data integrity is properly asserted.
     */
    protected function initTypeMapping()
    {
        if (empty($this->typeMapping)) {
            $defaultMapping = $this->getDefaultTypeMapping();

            Assertion::notEmpty(
                $defaultMapping,
                "Method [getDefaultTypeMapping] delivered an empty mapping array"
            );

            foreach ($defaultMapping as $type => $mapping) {
                $this->addTypeMappingEntry($type, $mapping);
            }
        }
    }

    /**
     * This defines the default type mappings.
     *
     * @return array
     */
    protected function getDefaultTypeMapping()
    {
        $mappings = array(
            'integer' => array(
                'nullable' => true, 'min' => PHP_INT_MAX * -1, 'max' => PHP_INT_MAX, 'default' => null,
            ),
            'float' => array(
                'nullable' => true, 'min' => PHP_INT_MAX * -1, 'max' => PHP_INT_MAX, 'default' => null,
            ),
            'bool' => array(
                'nullable' => false, 'default' => false,
            ),
            'string' => array(
                'nullable' => true, 'minLen' => 0, 'maxLen' => self::PHP_MAX_STR_LEN, 'default' => null,
            ),
            'array' => array(
                'nullable' => false, 'default' => [], 'itemType' => 'string',
            ),
            'entity' => array(
                'nullable' => true, 'default' => null, 'class' => null
            ),
        );

        return $mappings;
    }
}
