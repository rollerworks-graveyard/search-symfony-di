<?php

/**
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) 2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests\Extension\Symfony\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Compiler\ConditionOptimizerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class ConditionOptimizerPassTest extends AbstractCompilerPassTestCase
{
    const SERVICE_PREFIX = 'acme_user.search.condition_optimizer.';

    public function testRegisteringConditionOptimizers()
    {
        $collectingService = new Definition();
        $collectingService->setArguments(array(null, array(), array()));

        $this->setDefinition('rollerworks_search.chain_condition_optimizer', $collectingService);

        $this->createOptimizerService('first');
        $this->createOptimizerService('second');
        $this->createOptimizerService('last');
        $this->compile();

        $collectingService = $this->container->findDefinition('rollerworks_search.chain_condition_optimizer');
        $calls = $collectingService->getMethodCalls();

        $expectedCalls = array(
            array('addOptimizer', array(new Reference(self::SERVICE_PREFIX.'first'))),
            array('addOptimizer', array(new Reference(self::SERVICE_PREFIX.'second'))),
            array('addOptimizer', array(new Reference(self::SERVICE_PREFIX.'last'))),
        );

        $this->assertEquals($expectedCalls, $calls);
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConditionOptimizerPass());
    }

    private function createOptimizerService($name)
    {
        $collectedService = new Definition();
        $collectedService->addTag('rollerworks_search.condition_optimizer');
        $this->setDefinition(self::SERVICE_PREFIX.$name, $collectedService);
    }
}
