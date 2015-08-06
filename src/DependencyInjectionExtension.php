<?php

/*
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Extension\Symfony\DependencyInjection;

use Rollerworks\Component\Search\Exception\InvalidArgumentException;
use Rollerworks\Component\Search\SearchExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a way to lazy load types from the Symfony2 Dependency Injection container.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class DependencyInjectionExtension implements SearchExtensionInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string[]
     */
    private $types = [];

    /**
     * @var array[]
     */
    private $typeExtensions = [];

    /**
     * Constructor.
     *
     * @param ContainerInterface $container      Symfony services container object
     * @param string[]           $types          field-type service-ids (type => service-id )
     * @param array[]            $typeExtensions field-type extension service-ids (type => [[service-ids])
     */
    public function __construct(ContainerInterface $container, array $types, array $typeExtensions)
    {
        $this->container = $container;
        $this->types = $types;
        $this->typeExtensions = $typeExtensions;
    }

    /**
     * {@inheritdoc}
     */
    public function getType($name)
    {
        if (!isset($this->types[$name])) {
            throw new InvalidArgumentException(
                sprintf('The field type "%s" is not registered with the service container.', $name)
            );
        }

        $type = $this->container->get($this->types[$name]);

        if ($type->getName() !== $name) {
            throw new InvalidArgumentException(
                sprintf(
                    'The type name specified for the service "%s" does not match the actual name.'.
                    'Expected "%s", given "%s"',
                    $this->types[$name],
                    $name,
                    $type->getName()
                )
            );
        }

        return $type;
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($name)
    {
        return isset($this->types[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeExtensions($name)
    {
        $extensions = [];

        if (isset($this->typeExtensions[$name])) {
            foreach ($this->typeExtensions[$name] as $serviceId) {
                $extensions[] = $this->container->get($serviceId);
            }
        }

        return $extensions;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTypeExtensions($name)
    {
        return isset($this->typeExtensions[$name]);
    }
}
