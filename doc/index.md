Symfony DependencyInjection extension
=====================================

The RollerworksSearch Symfony DependencyInjection extension facilitates the
loading of the SearchFactory and lazy loading of field-types, field-type
extensions, FieldSets, Formatters (chain), input-processors and exporters.

**Note:** This document expects you are already familiar with the
[Symfony DependencyInjection component][1] and [RollerworksSearch][2]
it self.

## Introduction

To make registering services as straightforward as possible,
this package comes with a set of compiler-passes and service-files
to do most of the work for you.

**Note:** Because some services are lazy loaded from the container,
they must not be marked as private.

You can get the SearchFactory using the 'rollerworks_search.factory'
service name.

### Preparing the container

First you need to prepare the container, use following code to initialize
the ContainerBuilder.

**Note:** This doesn't include the types, formatters and such.
See the dedicated documentation for activating (and usage).

```php
use Rollerworks\Component\Search\Searches;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\DependencyInjectionExtension;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\ServiceLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

$container = new ContainerBuilder();

// First register the compiler passes to ensure lazy-loading works as expected
$container->addCompilerPass(new ExtensionPass());
$container->addCompilerPass(new InputProcessorPass());
$container->addCompilerPass(new ExporterPass());
$container->addCompilerPass(new FormatterPass());

// Load the core services (including the SearchFactory)
$serviceLoader = new ServiceLoader($container);
$serviceLoader->loadFile('services');
```

### Registering extensions

**Note:** Its advised to always register types in the ContainerBuilder
so they can be loaded lazily, only register search extensions when lazy
loading is not possible.

```php
$container
    ->register('acme.search.extension', 'Acme\Search\Extension\AcmeExtension')
    ->addTag('rollerworks_search.extension');
```

### Usage

Once you're done with building the Container, you need to compile it
so the services can actually be used.

```php
$container->compile();

$searchFactory = $container->get('rollerworks_search.factory');
```

* [Field types and type extensions](field_types.md)
* [Formatters](formatter.md)
* [Exporters](exporter.md)
* [Input processor](input.md)

[1]: http://symfony.com/doc/current/components/dependency_injection/introduction.html
[2]: https://github.com/rollerworks/RollerworksSearch
