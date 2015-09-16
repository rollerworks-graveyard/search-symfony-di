<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests\Extension\Symfony\DependencyInjection\PhpUnit;

use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory\FieldSet as DiFieldSet;
use Rollerworks\Component\Search\FieldConfigInterface;
use Rollerworks\Component\Search\FieldSet as RealFieldSet;

class FieldSetFieldEqualsConstraint extends \PHPUnit_Framework_Constraint
{
    /**
     * @var DiFieldSet
     */
    private $expectedFieldSet;

    public function __construct(DiFieldSet $expectedFieldSet)
    {
        $this->expectedFieldSet = $expectedFieldSet;
    }

    public function toString()
    {
        return sprintf(
          'is equal to %s',

          \PHPUnit_Util_Type::export($this->expectedFieldSet)
        );
    }

    public function evaluate($other, $description = '', $returnResult = false)
    {
        if (!$other instanceof RealFieldSet) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected an instance of Rollerworks\Component\Search\FieldSet, got "%s"',
                    is_object($other) ? get_class($other) : \PHPUnit_Util_Type::export($other)
                )
            );
        }

        /** @var RealFieldSet $other */
        if ($this->expectedFieldSet->getName() !== $other->getSetName()) {
            return false;
        }

        foreach ($this->expectedFieldSet as $name => $field) {
            if (!$other->has($name)) {
                $this->fail($other, sprintf('Missing field', $name));
            }

            $this->assertFieldEquals($field, $other->get($name));
        }

        return true;
    }

    private function assertFieldEquals(array $expectedField, FieldConfigInterface $actualField)
    {
        if ($actualField->getType() !== $expectedField['type']) {
            $this->fail(
                 $actualField,
                 sprintf(
                     'Type of "%s" ("%s") does not equal "%s"',
                     $actualField->getName(),
                     $actualField->getType(),
                     $expectedField['type']
                 )
            );
        }

        if (null !== $expectedField['model_class'] && $actualField->getModelRefClass() !== $expectedField['model_class']) {
            $this->fail(
                 $actualField,
                 sprintf(
                     'Class of "%s" ("%s") does not equal "%s"',
                     $actualField->getName(),
                     $actualField->getModelRefClass(),
                     $expectedField['class']
                 )
            );
        }

        if (null !== $expectedField['model_property'] && $actualField->getModelRefClass() !== $expectedField['model_property']) {
            $this->fail(
                 $actualField,
                 sprintf(
                     'Class property of "%s" ("%s") does not equal "%s"',
                     $actualField->getName(),
                     $actualField->getModelRefProperty(),
                     $expectedField['model_property']
                 )
            );
        }

        if ($actualField->isRequired() !== $expectedField['required']) {
            $this->fail(
                 $actualField,
                 sprintf(
                     'Required-state of "%s" ("%s") does not equal "%s"',
                     $actualField->getName(),
                     \PHPUnit_Util_Type::export($actualField->isRequired()),
                     \PHPUnit_Util_Type::export($expectedField['required'])
                 )
            );
        }

        $constraint = new \PHPUnit_Framework_Constraint_IsEqual(
            $expectedField['options']
        );

        $constraint->evaluate($actualField->getOptions());
    }
}
