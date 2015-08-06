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
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Compiler\ExporterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ExporterPassPassTest extends AbstractCompilerPassTestCase
{
    public function testRegisteringExporterPass()
    {
        $collectingService = new Definition();
        $collectingService->setArguments([null, []]);

        $this->setDefinition('rollerworks_search.exporter_factory', $collectingService);

        $collectedService = new Definition();
        $collectedService->addTag('rollerworks_search.exporter', ['alias' => 'jsonp']);
        $this->setDefinition('acme_user.search.exporter.jsonp', $collectedService);

        $this->compile();

        $collectingService = $this->container->findDefinition('rollerworks_search.exporter_factory');

        $this->assertNull($collectingService->getArgument(0));
        $this->assertEquals($collectingService->getArgument(1), ['jsonp' => 'acme_user.search.exporter.jsonp']);
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ExporterPass());
    }
}
