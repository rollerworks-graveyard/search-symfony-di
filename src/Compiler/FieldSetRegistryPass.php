<?php

/*
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass to register tagged fieldsets for the FieldSetRegistry.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class FieldSetRegistryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('rollerworks_search.fieldset_registry')) {
            return;
        }

        $fieldsets = array();
        foreach ($container->findTaggedServiceIds('rollerworks_search.fieldset') as $serviceId => $tag) {
            $name = isset($tag[0]['name']) ? $tag[0]['name'] : $serviceId;
            $fieldsets[$name] = $serviceId;
        }

        $definition = $container->getDefinition('rollerworks_search.fieldset_registry');
        $definition->replaceArgument(1, $fieldsets);
    }
}
