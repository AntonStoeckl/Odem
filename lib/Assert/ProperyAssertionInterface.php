<?php

namespace Odem\Assert;

interface ProperyAssertionInterface
{
    public static function assertValueIsType($value, $type);

    public static function assertValueIsValidType(array $propertyMapping, array $defaultMappings, $value);

    public static function assertValidPropertyDefinition(
        array $mapping,
        array $defaultMappings,
        $property,
        $entityName
    );

    public static function assertKnownPropertyType(array $defaultMappings, $type);
}
