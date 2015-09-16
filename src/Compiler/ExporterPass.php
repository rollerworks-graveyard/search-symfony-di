<?php

/*
 * This file is part of the RollerworksSearch package.
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
 * Compiler pass to register tagged services for an exporter.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ExporterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('rollerworks_search.exporter_factory')) {
            return;
        }

        $exporters = [];
        foreach ($container->findTaggedServiceIds('rollerworks_search.exporter') as $serviceId => $tag) {
            $container->findDefinition($serviceId)->setScope('prototype');

            $alias = isset($tag[0]['alias']) ? $tag[0]['alias'] : $serviceId;
            $exporters[$alias] = $serviceId;
        }

        $definition = $container->getDefinition('rollerworks_search.exporter_factory');
        $definition->replaceArgument(1, $exporters);
    }
}
