<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests\Extension\Symfony\DependencyInjection\Factory;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractContainerBuilderTestCase;
use Matthias\SymfonyServiceDefinitionValidator\Compiler\ValidateServiceDefinitionsPass;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Compiler\ExtensionPass;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory\FieldSet;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory\FieldSetFactory;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\ServiceLoader;
use Rollerworks\Component\Search\Tests\Extension\Symfony\DependencyInjection\PhpUnit\FieldSetFieldEqualsConstraint;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

class FieldSetFactoryTest extends AbstractContainerBuilderTestCase
{
    /**
     * @var FieldSetFactory
     */
    private $fieldSetFactory;

    public function testRegisterFieldSet()
    {
        $fieldSet = new FieldSet('acme_users');
        $fieldSet->set('id', 'integer', 'User', 'id');
        $fieldSet->set('name', 'text', null, null, true);
        $fieldSet->set('email', 'text', null, null, false, ['invalid_message' => 'this is not an email']);

        $this->fieldSetFactory->register($fieldSet);

        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            'rollerworks_search.fieldset.acme_users',
            'rollerworks_search.fieldset',
            ['name' => 'acme_users']
        );

        $this->container->compile();

        $this->assertThat(
            $this->container->get('rollerworks_search.fieldset.acme_users'),
            new FieldSetFieldEqualsConstraint($fieldSet)
        );
    }

    public function testCreateFieldSetBuilder()
    {
        $this->assertInstanceOf(
            'Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory\FieldSetBuilder',
            $this->fieldSetFactory->createFieldSetBuilder('acme_users')
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->container->addCompilerPass(new ExtensionPass());
        $this->container->addCompilerPass(new ValidateServiceDefinitionsPass(), PassConfig::TYPE_AFTER_REMOVING);
        $this->container->register('service_container', 'Symfony\Component\DependencyInjection\Container');

        new ServiceLoader($this->container);
        $this->fieldSetFactory = new FieldSetFactory(
            $this->container
        );
    }
}
