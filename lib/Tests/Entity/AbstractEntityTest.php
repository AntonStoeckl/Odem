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
        /** @var AbstractEntity|\PHPUnit_Framework_MockObject_MockObject $sut */
        $sut = $this->getMockBuilder('Odem\Entity\AbstractEntity')
            ->setMethods(array('addDefaultMapping'))
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $sut->expects($this->atLeastOnce())
            ->method('addDefaultMapping')
            ->with($this->isType('string'), $this->isType('array'));

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
    public function testGetMappingForPropertySuccessCase()
    {
        $this->markTestIncomplete('not all assertions done yet');

        $propertyMapping = array(
            'foo' => array('type' => 'integer', 'nullable' => false, 'min' => 1, 'max' => pow(2, 10)),
        );

        $this->sutGetMapping
            ->expects($this->once())
            ->method('getMapping')
            ->will($this->returnValue($propertyMapping));

        $sutMethod = new \ReflectionMethod($this->sutGetMapping, 'getMappingForProperty');
        $sutMethod->setAccessible(true);

        $actual = $sutMethod->invoke($this->sutGetMapping, 'foo');
    }
}
