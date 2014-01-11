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
     * @covers Odem\Entity\AbstractEntity::__construct()
     * @small
     */
    public function testConstructorHasSetDefaultMappingsSuccessCase()
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
}
