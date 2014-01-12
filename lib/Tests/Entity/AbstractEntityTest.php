<?php

namespace Odem\Tests\Entity;

use Odem\Tests\TestCase;
use Odem\Entity\AbstractEntity;

/**
 * Class AbstractEntityTest
 * @package Odem\Tests\Entity
 */
class AbstractEntityTest extends TestCase
{
    /**
     * SystemUnderTest mock instance with mehod "getMapping" mocked
     *
     * @var AbstractEntity|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sutGetMapping;

    /**
     * SystemUnderTest mock instance with no (existing) methods mocked
     *
     * @var AbstractEntity|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sutNothing;

    /**
     * SystemUnderTest mock instance with mehod "getMappingsForProperty" mocked
     *
     * @var AbstractEntity|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sutGetMappingForProperty;

    /**
     * Set up test instances.
     */
    public function setUp()
    {
        $this->sutGetMapping = $this->getMockBuilder('Odem\Entity\AbstractEntity')
            ->setMethods(array('getMapping'))
            ->getMockForAbstractClass();

        $this->sutNothing = $this->getMockBuilder('Odem\Entity\AbstractEntity')
            ->setMethods(array('_notExistingMethod_'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->sutGetMappingForProperty = $this->getMockBuilder('Odem\Entity\AbstractEntity')
            ->setMethods(array('getMappingForProperty'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * @covers Odem\Entity\AbstractEntity::__construct()
     * @small
     */
    public function testConstructorCallsDefaultMappingsSuccessCase()
    {
        /** @var AbstractEntity|\PHPUnit_Framework_MockObject_MockObject $sut */
        $sut = $this->getMockBuilder('Odem\Entity\AbstractEntity')
            ->setMethods(array('addDefaultMappings'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $sut->expects($this->once())
            ->method('addDefaultMappings');

        $sut->__construct();
    }

    /**
     * @covers Odem\Entity\AbstractEntity::addDefaultMappings()
     * @small
     */
    public function testAddDefaultMappingsSuccessCase()
    {
        // the expected keys in $this->defaultMappings after call to addDefaultMappings()
        $expectedTypes = array('integer', 'float', 'bool', 'string', 'array', 'entity');

        /** @var AbstractEntity|\PHPUnit_Framework_MockObject_MockObject $sut */
        $sut = $this->getMockBuilder('Odem\Entity\AbstractEntity')
            ->setMethods(array('addDefaultMapping'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        foreach ($expectedTypes as $index => $type) {
            $sut->expects($this->at($index))
                ->method('addDefaultMapping')
                ->with($type, $this->isType('array'));
        }

        $sutMethod = new \ReflectionMethod($sut, 'addDefaultMappings');
        $sutMethod->setAccessible(true);
        $sutMethod->invoke($sut);
    }

    /**
     * @covers Odem\Entity\AbstractEntity::addDefaultMapping()
     * @small
     */
    public function testAddDefaultMappingWithValidMappingAddsMappingSuccessCase()
    {
        $sutMethod = new \ReflectionMethod($this->sutNothing, 'addDefaultMapping');
        $sutMethod->setAccessible(true);

        $type = 'integer';
        $mapping = array('nullable' => true,);
        $sutMethod->invoke($this->sutNothing, $type, $mapping);

        $expected = array($type => $mapping);
        $this->assertAttributeEquals($expected, 'defaultMappings', $this->sutNothing);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     * @expectedExceptionMessage Expected 'type' to be a string
     * @covers Odem\Entity\AbstractEntity::addDefaultMapping()
     * @small
     */
    public function testAddDefaultMappingInvalidTypeErrorCase()
    {
        $sutMethod = new \ReflectionMethod($this->sutNothing, 'addDefaultMapping');
        $sutMethod->setAccessible(true);

        $type = array();
        $mapping = array('nullable' => true,);
        $sutMethod->invoke($this->sutNothing, $type, $mapping);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     * @expectedExceptionMessage Expected 'type' to be not empty
     * @covers Odem\Entity\AbstractEntity::addDefaultMapping()
     * @small
     */
    public function testAddDefaultMappingEmptyTypeErrorCase()
    {
        $sutMethod = new \ReflectionMethod($this->sutNothing, 'addDefaultMapping');
        $sutMethod->setAccessible(true);

        $type = '';
        $mapping = array('nullable' => true,);
        $sutMethod->invoke($this->sutNothing, $type, $mapping);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     * @expectedExceptionMessage Mapping for type [integer] already exists
     * @covers Odem\Entity\AbstractEntity::addDefaultMapping()
     * @small
     */
    public function testAddDefaultMappingTypeAlreadyExistsErrorCase()
    {
        $sutMethod = new \ReflectionMethod($this->sutNothing, 'addDefaultMapping');
        $sutMethod->setAccessible(true);

        $type = 'integer';
        $mapping = array('nullable' => true,);
        $sutMethod->invoke($this->sutNothing, $type, $mapping);
        $mapping = array('nullable' => true, 'min' => 1);
        $sutMethod->invoke($this->sutNothing, $type, $mapping);
    }

    /**
     * @covers Odem\Entity\AbstractEntity::getDefaultMappingForType()
     * @small
     */
    public function testGetDefaultMappingForTypeSuccessCase()
    {
        $sutMethod = new \ReflectionMethod($this->sutNothing, 'addDefaultMapping');
        $sutMethod->setAccessible(true);

        $type = 'integer';
        $mapping = array('nullable' => true, 'min' => 1, 'max' => 666);
        $sutMethod->invoke($this->sutNothing, $type, $mapping);

        $sutMethod = new \ReflectionMethod($this->sutNothing, 'getDefaultMappingForType');
        $sutMethod->setAccessible(true);

        $actual = $sutMethod->invoke($this->sutNothing, $type);
        $this->assertEquals($mapping, $actual);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     * @expectedExceptionMessage Expected 'type' to be a string
     * @covers Odem\Entity\AbstractEntity::getDefaultMappingForType()
     * @small
     */
    public function testGetDefaultMappingForTypeInvalidTypeErrorCase()
    {
        $sutMethod = new \ReflectionMethod($this->sutNothing, 'getDefaultMappingForType');
        $sutMethod->setAccessible(true);

        $type = array();
        $sutMethod->invoke($this->sutNothing, $type);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     * @expectedExceptionMessage Expected 'type' to be not empty
     * @covers Odem\Entity\AbstractEntity::getDefaultMappingForType()
     * @small
     */
    public function testGetDefaultMappingForTypeEmptyTypeErrorCase()
    {
        $sutMethod = new \ReflectionMethod($this->sutNothing, 'getDefaultMappingForType');
        $sutMethod->setAccessible(true);

        $type = '';
        $sutMethod->invoke($this->sutNothing, $type);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     * @expectedExceptionMessage No default mapping defined for type [notExistingType] defined
     * @covers Odem\Entity\AbstractEntity::getDefaultMappingForType()
     * @small
     */
    public function testGetDefaultMappingForTypeErrorCase()
    {
        $sutMethod = new \ReflectionMethod($this->sutNothing, 'addDefaultMapping');
        $sutMethod->setAccessible(true);

        $type = 'integer';
        $mapping = array('nullable' => true, 'min' => 1, 'max' => 666);
        $sutMethod->invoke($this->sutNothing, $type, $mapping);

        $sutMethod = new \ReflectionMethod($this->sutNothing, 'getDefaultMappingForType');
        $sutMethod->setAccessible(true);

        $sutMethod->invoke($this->sutNothing, 'notExistingType');
    }

    /**
     * @covers Odem\Entity\AbstractEntity::getMappingForProperty()
     * @small
     */
    public function testGetMappingForPropertyNotPlainSuccessCase()
    {
        $propertyMapping = array(
            'foo' => array('type' => 'integer', 'nullable' => false, 'min' => 1),
        );

        $this->sutGetMapping
            ->expects($this->once())
            ->method('getMapping')
            ->will($this->returnValue($propertyMapping));

        $sutMethod = new \ReflectionMethod($this->sutGetMapping, 'getMappingForProperty');
        $sutMethod->setAccessible(true);

        $expected = array(
            'type' => 'integer', 'nullable' => false, 'min' => 1, 'max' => PHP_INT_MAX, 'default' => null,
        );
        $actual = $sutMethod->invoke($this->sutGetMapping, 'foo');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Odem\Entity\AbstractEntity::getMappingForProperty()
     * @small
     */
    public function testGetMappingForPropertyWithPlainSuccessCase()
    {
        $propertyMapping = array(
            'foo' => array('type' => 'integer', 'nullable' => false, 'min' => 1),
        );

        $this->sutGetMapping
            ->expects($this->once())
            ->method('getMapping')
            ->will($this->returnValue($propertyMapping));

        $sutMethod = new \ReflectionMethod($this->sutGetMapping, 'getMappingForProperty');
        $sutMethod->setAccessible(true);

        $expected = array(
            'type' => 'integer', 'nullable' => false, 'min' => 1
        );
        $actual = $sutMethod->invoke($this->sutGetMapping, 'foo', true);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     * @expectedExceptionMessage Property [bar] not defined in this entity
     * @covers Odem\Entity\AbstractEntity::getMappingForProperty()
     * @small
     */
    public function testGetMappingForPropertyWithUndefPropertyErrorCase()
    {
        $propertyMapping = array(
            'foo' => array('type' => 'integer', 'nullable' => false, 'min' => 1),
        );

        $this->sutGetMapping
            ->expects($this->once())
            ->method('getMapping')
            ->will($this->returnValue($propertyMapping));

        $sutMethod = new \ReflectionMethod($this->sutGetMapping, 'getMappingForProperty');
        $sutMethod->setAccessible(true);

        $sutMethod->invoke($this->sutGetMapping, 'bar');
    }

    /**
     * @covers Odem\Entity\AbstractEntity::getEntityName()
     * @small
     */
    public function testGetEntityNameSuccessCase()
    {
        $mockClassname = 'AbstractEntityMock';
        $sutNothing = $this->getMockBuilder('Odem\Entity\AbstractEntity')
            ->setMethods(array('_notExistingMethod_'))
            ->disableOriginalConstructor()
            ->setMockClassName($mockClassname)
            ->getMockForAbstractClass();

        $sutMethod = new \ReflectionMethod($sutNothing, 'getEntityName');
        $sutMethod->setAccessible(true);

        $actual = $sutMethod->invoke($sutNothing);

        $this->assertEquals($mockClassname, $actual);
    }

    /**
     * @covers Odem\Entity\AbstractEntity::doSet()
     * @small
     */
    public function testDoSetIsFluidSuccessCase()
    {
        $propertyMapping = array(
            'foo' => array('type' => 'integer', 'nullable' => false, 'min' => 1),
        );
        $propertyKeys = array_keys($propertyMapping);
        $propertyKey = array_shift($propertyKeys);

        $this->sutGetMappingForProperty
            ->expects($this->once())
            ->method('getMappingForProperty')
            ->with($propertyKey)
            ->will($this->returnValue($propertyMapping));

        $sutMethod = new \ReflectionMethod($this->sutGetMappingForProperty, 'doSet');
        $sutMethod->setAccessible(true);

        $actual = $sutMethod->invoke($this->sutGetMappingForProperty, $propertyKey, 15);

        $this->assertSame($this->sutGetMappingForProperty, $actual);
    }
}
