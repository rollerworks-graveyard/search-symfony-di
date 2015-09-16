<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Extension\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * ServiceLoader Helps with the registering of services at
 * the ContainerBuilder.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ServiceLoader
{
    /**
     * @var XmlFileLoader
     */
    private $loader;

    /**
     * @param ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $this->loader->load('services.xml');
        $this->loader->load('type.xml');
    }

    /**
     * @param $serviceFile
     *
     * @return self
     */
    public function loadFile($serviceFile)
    {
        $this->loader->load($serviceFile.'.xml');

        return $this;
    }
}
