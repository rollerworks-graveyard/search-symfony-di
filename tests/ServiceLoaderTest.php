<?php

/**
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) 2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests\Extension\Symfony\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Matthias\SymfonyServiceDefinitionValidator\Compiler\ValidateServiceDefinitionsPass;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\ServiceLoader;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\Finder\Finder;

class ServiceLoaderTest extends AbstractContainerBuilderTestCase
{
    /**
     * @var ServiceLoader
     */
    private $serviceLoader;

    public function testLoadServiceFile()
    {
        $this->serviceLoader->loadFile('services');

        $this->assertContainerBuilderHasService(
            'rollerworks_search.factory',
            'Rollerworks\Component\Search\SearchFactory'
        );
    }

    /**
     * @dataProvider getServiceFiles
     */
    public function testLoadServiceFileIsValid($file)
    {
        $this->serviceLoader->loadFile($file);
        $this->compile();

        // Dummy, if there were no errors this test passes
        $this->assertTrue(true);
    }

    public static function getServiceFiles()
    {
        $finder = Finder::create()
            ->in(__DIR__.'/../Resources/config')
            ->files();

        $files = array();

        foreach ($finder as $file) {
            $files[] = array(substr($file, 0, -4));
        }

        return $files;
    }

    protected function setUp()
    {
        parent::setUp();

        $this->container->addCompilerPass(new ValidateServiceDefinitionsPass(), PassConfig::TYPE_AFTER_REMOVING);
        $this->container->register('service_container', 'Symfony\Component\DependencyInjection\Container');

        $this->serviceLoader = new ServiceLoader($this->container);
    }
}
