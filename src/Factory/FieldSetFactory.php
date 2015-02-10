<?php

/*
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory;

use Rollerworks\Component\Search\Metadata\MetadataReaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * FieldSetFactory, provides registering FieldSets as services.
 *
 * FieldSets must be provided as `Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory\FieldSet`
 * objects. Passing a 'real' FieldSet, is not possible as options may return
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

            if (!empty($field['model_class'])) {
                $this->setFactory($fieldDef, 'rollerworks_search.factory', 'createFieldForProperty');

                $fieldDef->addArgument($field['model_class']);
                $fieldDef->addArgument($field['model_property']);
            } else {
                $this->setFactory($fieldDef, 'rollerworks_search.factory', 'createField');
            }

            $fieldDef->addArgument($name);
            $fieldDef->addArgument($field['type']);
            $fieldDef->addArgument($field['options']);
            $fieldDef->addArgument($field['required']);

            $fieldSetDef->addMethodCall('set', array($name, $fieldDef));
        }

        $this->container->setDefinition(sprintf('rollerworks_search.fieldset.%s', $fieldSet->getName()), $fieldSetDef);
    }

    private function setFactory(Definition $definition, $serviceId, $method)
    {
        if (method_exists($definition, 'setFactory')) {
            $definition->setFactory(array(new Reference($serviceId), $method));
        } else {
            $definition->setFactoryService($serviceId);
            $definition->setFactoryMethod($method);
        }
    }
}
