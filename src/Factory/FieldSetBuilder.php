<?php

/**
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) 2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory;

use Rollerworks\Component\Search\Exception\BadMethodCallException;
use Rollerworks\Component\Search\Exception\InvalidArgumentException;
use Rollerworks\Component\Search\Exception\UnexpectedTypeException;
use Rollerworks\Component\Search\Metadata\MetadataReaderInterface;
use Rollerworks\Component\Search\Metadata\NullMetadataReader;

/**
 * A builder for creating FieldSet instances.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class FieldSetBuilder
{
    /**
     * @var array
     */
    protected $fields = array();

    /**
     * @var MetadataReaderInterface
     */
    protected $mappingReader;

    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param string                  $name
     * @param MetadataReaderInterface $mappingReader
     */
    public function __construct($name, MetadataReaderInterface $mappingReader = null)
    {
        $this->name = $name;
        $this->mappingReader = $mappingReader ?: new NullMetadataReader();
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
     * @param string $field
     * @param string $type
     * @param array  $options
     * @param bool   $required
     * @param string $modelClass
     * @param string $property
     *
     * @return self
     *
     * @throws UnexpectedTypeException
     */
    public function set($field, $type = null, array $options = array(), $required = false, $modelClass = null, $property = null)
    {
        if (!is_string($field)) {
            throw new UnexpectedTypeException($field, 'string');
        }

        if (null !== $type && !is_string($type)) {
            throw new UnexpectedTypeException($type, 'string" or "null');
        }

        $this->fields[$field] = array(
            'type' => $type,
            'options' => $options,
            'required' => $required,
            'class' => $modelClass,
            'property' => $property
        );

        return $this;
    }

    /**
     * Removes the field from this FieldSet.
     *
     * @param string $name
     *
     * @return self
     *
     * @throws BadMethodCallException
     */
    public function remove($name)
    {
        unset($this->fields[$name]);

        return $this;
    }

    /**
     * Returns whether the field is registered at this FieldSet.
     *
     * @param string $name
     *
     * @return boolean
     *
     * @throws BadMethodCallException
     */
    public function has($name)
    {
        return isset($this->fields[$name]);
    }

    /**
     * Gets the field-configuration by name.
     *
     * @param string $name
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function get($name)
    {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        throw new InvalidArgumentException(sprintf('The field with the name "%s" does not exist.', $name));
    }

    /**
     * Imports the search fields using the mapping-data of a class.
     *
     * Mapping Data is provided using the MappingReader.
     * When no metadata is found an exception gets thrown.
     *
     * Note. you can only use include or exclude, not both.
     * Use the field-name, not the property-name!
     *
     * @param string $class
     * @param array  $include List of field names to use, everything else is excluded
     * @param array  $exclude List of field names to exclude
     *
     * @return self
     *
     * @throws \RuntimeException when there is no MappingReader set
     */
    public function importFromClass($class, array $include = array(), array $exclude = array())
    {
        $metadata = $this->mappingReader->getSearchFields($class);

        if (!$metadata) {
            throw new \RuntimeException(
                sprintf('FieldSetBuilder is unable to import metadata from "%s", no metadata was found.', $class)
            );
        }

        foreach ($metadata as $field) {
            if (!$this->isFieldAccepted($field->fieldName, $include, $exclude)) {
                continue;
            }

            $this->fields[$field->fieldName] = array(
                'type' => $field->type,
                'options' => $field->options,
                'required' => $field->required,
                'class' => $field->class,
                'property' => $field->property
            );
        }

        return $this;
    }

    /**
     * Returns the configured FieldSet object.
     *
     * @return FieldSet
     */
    public function getFieldSet()
    {
        $fieldSet = new FieldSet($this->name);

        foreach ($this->fields as $name => $field) {
            $fieldSet->set(
                $name,
                $field['type'],
                $field['class'],
                $field['property'],
                $field['required'],
                $field['options']
            );
        }

        return $fieldSet;
    }

    /**
     * Must the field be included in the import process.
     *
     * @param string $field
     * @param array  $include
     * @param array  $exclude
     *
     * @return bool
     */
    protected function isFieldAccepted($field, $include = array(), $exclude = array())
    {
        if ($include) {
            return in_array($field, $include, true);
        }

        if ($exclude) {
            return !in_array($field, $exclude, true);
        }

        return true;
    }
}
