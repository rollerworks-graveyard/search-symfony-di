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
use Rollerworks\Component\Search\FieldSet;
use Rollerworks\Component\Search\FieldSetRegistryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class FieldSetRegistry implements FieldSetRegistryInterface
{
    /**
     * @var string[]
     */
    private $serviceIds = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param array              $serviceIds
     */
    public function __construct(ContainerInterface $container, array $serviceIds)
    {
        $this->container = $container;
        $this->serviceIds = $serviceIds;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name)
    {
        if (!isset($this->serviceIds[$name])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Unable to get FieldSet "%s", FieldSet does not seem to be registered in the Service Container.',
                    $name
                )
            );
        }

        return $this->container->get($this->serviceIds[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        return isset($this->serviceIds[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function add(FieldSet $fieldSet)
    {
        $name = $fieldSet->getSetName();

        if (isset($this->serviceIds[$name])) {
            throw new InvalidArgumentException(sprintf('Unable to overwrite already registered FieldSet "%s".', $name));
        }

        if (!$fieldSet->isConfigLocked()) {
            throw new InvalidArgumentException(sprintf('Unable to register none configuration-locked FieldSet "%s".', $name));
        }

        $serviceId = 'rollerworks_search.fieldset.late_registering.'.$name;

        $this->serviceIds[$name] = $serviceId;
        $this->container->set($serviceId, $fieldSet);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->serviceIds;
    }
}
