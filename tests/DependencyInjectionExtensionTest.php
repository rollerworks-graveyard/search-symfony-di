<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests\Extension\Symfony\DependencyInjection;

use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Compiler\ExtensionPass;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\ServiceLoader;
use Rollerworks\Component\Search\SearchFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DependencyInjectionExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $serviceContainer;

    protected function setUp()
    {
        parent::setUp();

        $this->serviceContainer = new ContainerBuilder();
        $this->serviceContainer->addCompilerPass(new ExtensionPass());

        new ServiceLoader($this->serviceContainer);
        $this->serviceContainer->compile();
    }

    public function testGetType()
    {
        $this->assertInstanceOf(
            'Rollerworks\Component\Search\FieldConfigInterface',
            $this->getFactory()->createField('name', 'text')
        );
    }

    /**
     * @return SearchFactory
     */
    private function getFactory()
    {
        return $this->serviceContainer->get('rollerworks_search.factory');
    }
}
