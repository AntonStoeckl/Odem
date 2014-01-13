<?php

namespace Odem\Assert;

interface ProperyAssertionInterface
{
    public function assertValueIsType($value, $type);

    public function assertValueIsValidType(array $propertyMapping, array $defaultMappings, $value);

    public function assertValidPropertyDefinition(
        array $mapping,
        array $defaultMappings,
        $property,
        $entityName
    );

    public function assertKnownPropertyType(array $defaultMappings, $type);
}
