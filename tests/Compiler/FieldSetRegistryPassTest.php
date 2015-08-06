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
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Compiler\FieldSetRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class FieldSetRegistryPassTest extends AbstractCompilerPassTestCase
{
    public function testRegisteringExporterPass()
    {
        $collectingService = new Definition();
        $collectingService->setArguments([null, []]);

        $this->setDefinition('rollerworks_search.fieldset_registry', $collectingService);

        $collectedService = new Definition();
        $collectedService->addTag('rollerworks_search.fieldset', ['name' => 'acme_user']);
        $this->setDefinition('rollerworks_search.fieldset.acme_user', $collectedService);

        $this->compile();

        $collectingService = $this->container->findDefinition('rollerworks_search.fieldset_registry');

        $this->assertNull($collectingService->getArgument(0));
        $this->assertEquals($collectingService->getArgument(1), ['acme_user' => 'rollerworks_search.fieldset.acme_user']);
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FieldSetRegistryPass());
    }
}
