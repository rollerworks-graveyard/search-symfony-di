<?php

/*
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests\Extension\Symfony\DependencyInjection\Factory;

use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory\FieldSet;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory\FieldSetBuilder;
use Rollerworks\Component\Search\Metadata\MetadataReaderInterface;
use Rollerworks\Component\Search\Metadata\SearchField;

class FieldSetBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FieldSetBuilder
     */
    private $fieldSetBuilder;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|MetadataReaderInterface
     */
    private $metadataReader;

    public function testGetName()
    {
        $this->assertEquals('acme_user', $this->fieldSetBuilder->getName());
    }

    public function testSetField()
    {
        $expectedFieldSet = new FieldSet('acme_user');
        $expectedFieldSet->set('id', 'integer', 'stdClass', 'id', true);
        $expectedFieldSet->set('name', 'text', null, null, false, array('invalid_message' => 'whoops'));

        $this->fieldSetBuilder->set('id', 'integer', array(), true, 'stdClass', 'id');
        $this->fieldSetBuilder->set('name', 'text', array('invalid_message' => 'whoops'));

        $actualFieldSet = $this->fieldSetBuilder->getFieldSet();
        $this->assertEquals($expectedFieldSet, $actualFieldSet);

        $this->assertTrue($this->fieldSetBuilder->has('id'), 'must have a field named "id"');
        $this->assertFalse($this->fieldSetBuilder->has('email'), 'must have no field named "email"');

        $this->assertEquals(
            array(
                'type' => 'integer',
                'options' => array(),
                'required' => true,
                'class' => 'stdClass',
                'property' => 'id',
            ),
            $this->fieldSetBuilder->get('id'),
            'field "id" must equal expected field'
        );
    }

    public function testRemoveField()
    {
        $this->fieldSetBuilder->set('id', 'integer', array(), true, 'stdClass', 'id');
        $this->fieldSetBuilder->set('name', 'text', array('invalid_message' => 'whoops'));

        $this->assertTrue($this->fieldSetBuilder->has('id'), 'must have a field named "id"');
        $this->assertFalse($this->fieldSetBuilder->has('email'), 'must have no field named "email"');

        $this->fieldSetBuilder->remove('id');
        $this->assertFalse($this->fieldSetBuilder->has('id'), 'must have no field named "id" after remove');
    }

    public function testGetNoneRegisteredFieldGivesError()
    {
        $this->fieldSetBuilder->set('id', 'integer', array(), true, 'stdClass', 'id');

        $this->setExpectedException('InvalidArgumentException', 'The field with the name "name" does not exist.');
        $this->fieldSetBuilder->get('name');
    }

    public function testImportFromClass()
    {
        $class = 'Rollerworks\Component\Search\Tests\Stub\ECommerceInvoice';

        $expectedFieldSet = new FieldSet('acme_user');
        $expectedFieldSet->set('id', 'integer', $class, 'id', true);
        $expectedFieldSet->set('user_name', 'text', $class, 'name', false, array('invalid_message' => 'whoops'));

        $fields = array(
            'id' => new SearchField('id', $class, 'id', true, 'integer'),
            'user_name' => new SearchField('user_name', $class, 'name', false, 'text', array('invalid_message' => 'whoops')),
        );

        $this->metadataReader
            ->expects($this->atLeastOnce())
            ->method('getSearchFields')
            ->with($class)
            ->will($this->returnValue($fields))
        ;

        $this->fieldSetBuilder->importFromClass($class);

        $actualFieldSet = $this->fieldSetBuilder->getFieldSet();
        $this->assertEquals($expectedFieldSet, $actualFieldSet);
    }

    public function testImportFromClassWithInclude()
    {
        $class = 'Rollerworks\Component\Search\Tests\Stub\ECommerceInvoice';

        $expectedFieldSet = new FieldSet('acme_user');
        $expectedFieldSet->set('id', 'integer', $class, 'id', true);

        $fields = array(
            'id' => new SearchField('id', $class, 'id', true, 'integer'),
            'user_name' => new SearchField('user_name', $class, 'name', false, 'text', array('invalid_message' => 'whoops')),
        );

        $this->metadataReader
            ->expects($this->atLeastOnce())
            ->method('getSearchFields')
            ->with($class)
            ->will($this->returnValue($fields))
        ;

        $this->fieldSetBuilder->importFromClass($class, array('id'));

        $actualFieldSet = $this->fieldSetBuilder->getFieldSet();
        $this->assertEquals($expectedFieldSet, $actualFieldSet);
    }

    public function testImportFromClassWithExclude()
    {
        $class = 'Rollerworks\Component\Search\Tests\Stub\ECommerceInvoice';

        $expectedFieldSet = new FieldSet('acme_user');
        $expectedFieldSet->set('id', 'integer', $class, 'id', true);

        $fields = array(
            'id' => new SearchField('id', $class, 'id', true, 'integer'),
            'user_name' => new SearchField('user_name', $class, 'name', false, 'text', array('invalid_message' => 'whoops')),
        );

        $this->metadataReader
            ->expects($this->atLeastOnce())
            ->method('getSearchFields')
            ->with($class)
            ->will($this->returnValue($fields))
        ;

        $this->fieldSetBuilder->importFromClass($class, array(), array('user_name'));

        $actualFieldSet = $this->fieldSetBuilder->getFieldSet();
        $this->assertEquals($expectedFieldSet, $actualFieldSet);
    }

    protected function setUp()
    {
        $this->metadataReader = $this->getMock('Rollerworks\Component\Search\Metadata\MetadataReaderInterface');
        $this->fieldSetBuilder = new FieldSetBuilder('acme_user', $this->metadataReader);
    }
}
