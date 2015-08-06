<?php

/*
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests\Extension\Symfony\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Rollerworks\Component\Search\Tests\Extension\Symfony\DependencyInjection\Stub\ConcreteChainResolverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ChainFieldAliasResolverPassTest extends AbstractCompilerPassTestCase
{
    public function testRegisteringInputProcessors()
    {
        $collectingService = new Definition();
        $collectingService->setArguments([null, []]);

        $this->setDefinition('rollerworks_search.concrete_resolver.chain', $collectingService);

        $collectedService = new Definition();
        $collectedService->addTag('rollerworks_search.concrete_resolver');
        $this->setDefinition('acme_user.search.concrete_resolver.jsonp', $collectedService);

        $this->compile();

        $collectingService = $this->container->findDefinition('rollerworks_search.concrete_resolver.chain');

        $this->assertNull($collectingService->getArgument(0));
        $this->assertEquals($collectingService->getArgument(1), ['acme_user.search.concrete_resolver.jsonp']);
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConcreteChainResolverPass());
    }
}
