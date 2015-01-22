InputProcessor
==============

## Registering

Input processors must be tagged as `rollerworks_search.input_processor`,
with "alias" as additional parameter.

**Note:** Alias is a shortcut for getting the input processor, else the
service-id is used.

```php
$container
    ->register('rollerworks_search.input_processor.filter_query', 'Rollerworks\Component\Search\Input\FilterQuery')
    ->addTag('rollerworks_search.input_processor', array('alias' => 'filter_query'));
```

**Note:** input processors are reusable, the factory is only to help with
the lazy loading of them and ensuring only input processors are loaded.

## Loading

To load an input processor, use the `rollerworks_search.input_factory` service
which returns a `Rollerworks\Component\Search\InputProcessorInterface` instance.

```php
// get() expects the processor alias or service-id
$exporter = $container->get('rollerworks_search.exporter_factory')->get('filter_query');
```

## Loading bundled input processors

You can load the bundled input processors by importing the 'input_processor.xml'
service-definition file from this package.

```php
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\ServiceLoader;

$serviceLoader = new ServiceLoader($container);
$serviceLoader->loadFile('services'); // this ensures the required core services are registered
$serviceLoader->loadFile('input_processor');
```
