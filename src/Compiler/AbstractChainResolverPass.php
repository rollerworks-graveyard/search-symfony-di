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
 * Abstract chain-resolver compiler pass register services tagged as
 * 'rollerworks_search.label_resolver' on the
 * 'rollerworks_search.label_resolver.chain' service.
 *
 * @internal
 */
abstract class AbstractChainResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $chainServiceId = sprintf('rollerworks_search.%s.chain', $this->getChainType());

        if (!$container->hasDefinition($chainServiceId)) {
            return;
        }

        $container->getDefinition($chainServiceId)->replaceArgument(
            0,
            array_keys($container->findTaggedServiceIds(sprintf('rollerworks_search.%s', $this->getChainType())))
        );
    }

    abstract protected function getChainType();
}
