<?php

namespace Entity;

use Odem\Entity\AbstractEntity;

class Foobar extends AbstractEntity
{
    /**
     * {@inheritdoc}
     */
    public function getMapping()
    {
        $mapping = array(
            'foo' => array('type' => 'integer', 'nullable' => false, 'min' => 1, 'max' => pow(2, 10)),
            'bar' => array('type' => 'bool')
        );

        return $mapping;
    }
}
