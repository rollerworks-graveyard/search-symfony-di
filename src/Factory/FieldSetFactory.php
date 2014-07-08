<?php

/**
 * This file is part of the RollerworksSearchBundle package.
 *
 * (c) 2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\SearchBundle\DependencyInjection\Factory;

use Rollerworks\Component\Search\Metadata\MetadataReaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * FieldSetFactory, provides registering FieldSets as services.
 *
 * FieldSets must be provided as `Rollerworks\Bundle\SearchBundle\DependencyInjection\Factory\FieldSet` objects.
 * Passing a 'real' FieldSet, is not possible as options may return
 * a none exportable value, like a closure.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class FieldSetFactory
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var MetadataReaderInterface
     */
    private $metadataReader;

    /**
     * Constructor.
     *
     * @param ContainerBuilder        $container      ContainerBuilder to register the fieldsets at
     * @param MetadataReaderInterface $metadataReader Optional MetadataReader for importing search-fields.
     *                                                Only required if you want to use createFieldSetBuilder()
     *                                                with importFromClass().
     */
    public function __construct(ContainerBuilder $container, MetadataReaderInterface $metadataReader = null)
    {
        $this->container = $container;
        $this->metadataReader = $metadataReader;
    }

    /**
     * Returns a new FieldSetBuilder object.
     *
     * @param string $name
     *
     * @return FieldSetBuilder
     */
    public function createFieldSetBuilder($name)
    {
        return new FieldSetBuilder($name, $this->metadataReader);
    }

    /**
     * Registers the fieldset in the Service container.
     *
     * The FieldSet is registered as 'rollerworks_search.fieldset.[FieldSetName]'.
     *
     * @param FieldSet $fieldSet
     */
    public function register(FieldSet $fieldSet)
    {
        $fieldSetDef = new Definition('Rollerworks\Component\Search\FieldSet');
        $fieldSetDef->addArgument($fieldSet->getName());
        $fieldSetDef->addTag('rollerworks_search.fieldset', array('name' => $fieldSet->getName()));

        foreach ($fieldSet->all() as $name => $field) {
            $fieldDef = new Definition();
            $fieldDef->setFactoryService('rollerworks_search.factory');

            if (!empty($field['model_class'])) {
                $fieldDef->setFactoryMethod('createFieldForProperty');
                $fieldDef->addArgument($field['model_class']);
                $fieldDef->addArgument($field['model_property']);
            } else {
                $fieldDef->setFactoryMethod('createField');
            }

            $fieldDef->addArgument($field['type']);
            $fieldDef->addArgument($field['options']);
            $fieldDef->addArgument($field['required']);

            $fieldSetDef->addMethodCall('set', array($name, $fieldDef));
        }

        $this->container->setDefinition(sprintf('rollerworks_search.fieldset.%s', $fieldSet->getName()), $fieldSetDef);
    }
}