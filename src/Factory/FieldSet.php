<?php

/**
 * This file is part of the RollerworksSearchBundle package.
 *
 * (c) 2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\SearchBundle\DependencyInjection\Factory;

/**
 * FieldSet keeps the configuration for registering a 'real' fieldset
 * in the Symfony Service container.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class FieldSet
{
    /**
     * @var array
     */
    private $fields = array();

    /**
     * @var string
     */
    private $name;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        \Rollerworks\Component\Search\FieldSet::validateName($name);

        $this->name = $name;
    }

    /**
     * Returns the name of the fieldset.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set a search field-configuration on this FieldSet.
     *
     * @param string      $name
     * @param string|null $type
     * @param string|null $modelClass
     * @param string|null $modelProperty
     * @param bool        $required
     * @param array       $options
     *
     * @return FieldSet
     */
    public function set($name, $type = null, $modelClass = null, $modelProperty = null, $required = false, array $options = array())
    {
        $this->fields[$name] = array(
            'type' => $type,
            'model_class' => $modelClass,
            'model_property' => $modelProperty,
            'required' => $required,
            'options' => $options,
        );

        return $this;
    }

    /**
     * Gets the field-configuration by name.
     *
     * @param string $name
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        if (!isset($this->fields[$name])) {
            throw new \InvalidArgumentException('Field "%s" is not registered in the FieldSet.', $name);
        }

        return $this->fields[$name];
    }

    /**
     * Returns whether the field is registered at this FieldSet.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * Removes the field from this FieldSet.
     *
     * @param string $name
     *
     * @return self
     */
    public function remove($name)
    {
        if (isset($this->fields[$name])) {
            unset($this->fields[$name]);
        }

        return $this;
    }

    /**
     * Returns all the registered fields.
     *
     * @return array[]
     */
    public function all()
    {
        return $this->fields;
    }
}
