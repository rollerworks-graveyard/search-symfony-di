<?php

/**
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) 2014 Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace spec\Rollerworks\Component\Search\Extension\Symfony\DependencyInjection;

use PhpSpec\ObjectBehavior;
use Rollerworks\Component\Search\Exception\InvalidArgumentException;
use Rollerworks\Component\Search\FieldTypeExtensionInterface;
use Rollerworks\Component\Search\FieldTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DependencyInjectionExtensionSpec extends ObjectBehavior
{
    public function let(ContainerInterface $container)
    {
        $this->beConstructedWith($container, array(), array());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(
             'Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\DependencyInjectionExtension'
        );
    }

    public function it_has_types(ContainerInterface $container, FieldTypeInterface $fieldType, FieldTypeInterface $fieldType2)
    {
        $fieldType->getName()->willReturn('foo');
        $fieldType2->getName()->willReturn('bar');

        $container->get('foo_type')->willReturn($fieldType);
        $container->get('bar_type')->willReturn($fieldType2);

        $this->beConstructedWith($container, array('foo' => 'foo_type', 'bar' => 'bar_type'), array());

        $this->hasType('foo')->shouldReturn(true);
        $this->hasType('bar')->shouldReturn(true);
        $this->hasType('car')->shouldReturn(false);
    }

    public function it_gets_a_type(ContainerInterface $container, FieldTypeInterface $fieldType, FieldTypeInterface $fieldType2)
    {
        $fieldType->getName()->willReturn('foo');
        $fieldType2->getName()->willReturn('bar');

        $container->get('foo_type')->willReturn($fieldType);
        $container->get('bar_type')->willReturn($fieldType2);

        $this->beConstructedWith($container, array('foo' => 'foo_type', 'bar' => 'bar_type'), array());

        $this->getType('foo')->shouldReturn($fieldType);
    }

    public function it_validates_type_name_with_alias(ContainerInterface $container, FieldTypeInterface $fieldType)
    {
        $fieldType->getName()->willReturn('fool');
        $container->get('foo_type')->willReturn($fieldType);

        $this->beConstructedWith($container, array('foo' => 'foo_type', 'bar' => 'bar_type'), array());

        $this->shouldThrow(
            new InvalidArgumentException(
                'The type name specified for the service "foo_type" does not match the actual name.'.
                'Expected "foo", given "fool"'
            )
        )->during('getType', array('foo'));
    }

    public function it_throws_an_exception_when_getting_a_none_existing_type()
    {
        $this->shouldThrow(
            new InvalidArgumentException(
                'The field type "foo" is not registered with the service container.'
            )
        )->during('getType', array('foo'));
    }

    public function it_has_type_extensions(ContainerInterface $container, FieldTypeExtensionInterface $fieldTypeExtension)
    {
        $fieldTypeExtension->getExtendedType()->willReturn('foo');
        $container->get('foo_type.extension')->willReturn(array($fieldTypeExtension));

        $this->beConstructedWith($container, array(), array('foo' => 'foo_type.extension'));

        $this->hasTypeExtensions('foo')->shouldReturn(true);
        $this->hasTypeExtensions('bar')->shouldReturn(false);
    }

    public function it_gets_a_type_extensions(ContainerInterface $container, FieldTypeExtensionInterface $fieldTypeExtension)
    {
        $fieldTypeExtension->getExtendedType()->willReturn('foo');
        $container->get('foo_type.extension')->willReturn($fieldTypeExtension);

        $this->beConstructedWith($container, array(), array('foo' => array('foo_type.extension')));

        $this->getTypeExtensions('foo')->shouldReturn(array($fieldTypeExtension));
    }
}
