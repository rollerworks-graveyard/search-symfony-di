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
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Compiler\ExtensionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ExtensionPassTest extends AbstractCompilerPassTestCase
{
    public function testRegisteringOfSearchTypes()
    {
        $collectingService = new Definition();
        $collectingService->setArguments(array(null, array(), array()));

        $this->setDefinition('rollerworks_search.extension', $collectingService);

        $collectedService = new Definition();
        $collectedService->addTag('rollerworks_search.type', array('alias' => 'user_id'));
        $this->setDefinition('acme_user.search.type.user_id', $collectedService);

        $this->compile();

        $collectingService = $this->container->findDefinition('rollerworks_search.extension');

        $this->assertNull($collectingService->getArgument(0));
        $this->assertEquals(array('user_id' => 'acme_user.search.type.user_id'), $collectingService->getArgument(1));
        $this->assertCount(0, $collectingService->getArgument(2));
    }

    public function testRegisteringOfSearchTypesExtensions()
    {
        $collectingService = new Definition();
        $collectingService->setArguments(array(null, array(), array()));

        $this->setDefinition('rollerworks_search.extension', $collectingService);

        $collectedService = new Definition();
        $collectedService->addTag('rollerworks_search.type_extension', array('alias' => 'field'));
        $this->setDefinition('acme_user.search.type_extension.field', $collectedService);

        $this->compile();

        $collectingService = $this->container->findDefinition('rollerworks_search.extension');

        $this->assertNull($collectingService->getArgument(0));
        $this->assertCount(0, $collectingService->getArgument(1));
        $this->assertEquals(
             array('field' => array('acme_user.search.type_extension.field')),
             $collectingService->getArgument(2)
        );
    }

    public function testRegisteringOfSearchExtensions()
    {
        $extensionDefinition = new Definition();
        $extensionDefinition->setArguments(array(null, array(), array()));
        $this->setDefinition('rollerworks_search.extension', $extensionDefinition);

        $collectingService = new Definition();
        $collectingService->setArguments(
            array(
                array(new Reference('rollerworks_search.extension')),
            )
        );

        $this->setDefinition('rollerworks_search.registry', $collectingService);

        $collectedService = new Definition('DoctrineOrmExtension');
        $collectedService->addTag('rollerworks_search.extension');
        $this->setDefinition('rollerworks_search.extension.doctrine_orm', $collectedService);

        $this->compile();

        $collectingService = $this->container->findDefinition('rollerworks_search.registry');

        $this->assertEquals(
            $collectingService->getArgument(0),
            array(
                new Reference('rollerworks_search.extension'),
                new Reference('rollerworks_search.extension.doctrine_orm'),
            )
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ExtensionPass());
    }
}
