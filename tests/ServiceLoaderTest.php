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

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Matthias\SymfonyServiceDefinitionValidator\Compiler\ValidateServiceDefinitionsPass;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\ServiceLoader;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

class ServiceLoaderTest extends AbstractContainerBuilderTestCase
{
    /**
     * @var ServiceLoader
     */
    private $serviceLoader;

    public function testCoreServicesAreRegistered()
    {
        $this->assertContainerBuilderHasService(
            'rollerworks_search.factory',
            'Rollerworks\Component\Search\SearchFactory'
        );
    }

    /**
     * @dataProvider getServiceFiles
     *
     * @param string $file
     */
    public function testLoadServiceFileIsValid($file)
    {
        $container = new ContainerBuilder();
        $container->register('service_container', 'Symfony\Component\DependencyInjection\Container');
        $container->addCompilerPass(new ValidateServiceDefinitionsPass(), PassConfig::TYPE_AFTER_REMOVING);

        $serviceLoader = new ServiceLoader($container);
        $serviceLoader->loadFile($file);

        try {
            $container->compile();
        } catch (\Exception $e) {
            if ($e->getMessage() !== 'The service definition "rollerworks_search.metadata_factory" does not exist.') {
                throw $e;
            }
        }

        $this->assertInstanceOf(
            'Rollerworks\Component\Search\SearchFactory', $container->get('rollerworks_search.factory')
        );
    }

    public static function getServiceFiles()
    {
        $finder = Finder::create()
            ->in(__DIR__.'/../Resources/config')
            ->files();

        $files = [];

        foreach ($finder as $file) {
            $files[] = [substr($file, 0, -4)];
        }

        return $files;
    }

    protected function setUp()
    {
        parent::setUp();

        // We can't register the ValidateServiceDefinitionsPass as services are not resolved
        $this->container->register('service_container', 'Symfony\Component\DependencyInjection\Container');

        $this->serviceLoader = new ServiceLoader($this->container);
    }
}
