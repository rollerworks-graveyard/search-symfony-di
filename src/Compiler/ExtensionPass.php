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
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Adds all services with the tags "rollerworks_search.type" and "rollerworks_search.type_extension" as
 * arguments of the "rollerworks_search.extension" service.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ExtensionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('rollerworks_search.extension')) {
            return;
        }

        $definition = $container->getDefinition('rollerworks_search.extension');

        $this->processExtensions($container);
        $this->processTypes($definition, $container);
        $this->processTypeExtensions($definition, $container);
    }

    private function processExtensions(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('rollerworks_search.registry')) {
            return;
        }

        $definition = $container->getDefinition('rollerworks_search.registry');
        $extensions = $definition->getArgument(0);

        foreach (array_keys($container->findTaggedServiceIds('rollerworks_search.extension')) as $serviceId) {
            $extensions[] = new Reference($serviceId);
        }

        $definition->replaceArgument(0, $extensions);
    }

    private function processTypes(Definition $definition, ContainerBuilder $container)
    {
        $types = array();

        foreach ($container->findTaggedServiceIds('rollerworks_search.type') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias']) ? $tag[0]['alias'] : $serviceId;
            // Flip, because we want tag aliases (= type identifiers) as keys
            $types[$alias] = $serviceId;
        }

        $definition->replaceArgument(1, $types);
    }

    private function processTypeExtensions(Definition $definition, ContainerBuilder $container)
    {
        $typeExtensions = array();

        foreach ($container->findTaggedServiceIds('rollerworks_search.type_extension') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;

            $typeExtensions[$alias][] = $serviceId;
        }

        $definition->replaceArgument(2, $typeExtensions);
    }
}
