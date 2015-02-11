Exporters
=========

Exporters are used for exporting a SearchCondition to a portable format
like an array or specially formatted string.

## Registering

SearchCondition exporters must be tagged as `rollerworks_search.exporter`,
with "alias" as additional parameter.

**Note:** Alias is a shortcut for getting the exporter, else the service-id
is used instead.

```php
$container
    ->register(
        'rollerworks_search.exporter.filter_query',
        'Rollerworks\Component\Search\Exporter\FilterQueryExporter'
    )
    ->addTag('rollerworks_search.exporter', array('alias' => 'filter_query'));
```

**Caution:** To ensure a new exporter is returned every time, the services
tagged as exporter will be updated with scope prototype.

## Loading

To get a new exporter, use the `rollerworks_search.exporter_factory` service
which returns a new `Rollerworks\Component\Search\ExporterInterface` instance.

```php
// create() expects the exporter alias or service-id
$exporter = $container->get('rollerworks_search.exporter_factory')->create('filter_query');
```

## Loading bundled exporters

You can load the bundled exporters by importing the 'exporter.xml'
service-definition file from this package.

```php
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\ServiceLoader;

$serviceLoader = new ServiceLoader($container);
$serviceLoader->loadFile('services'); // this ensures the required core services are registered
$serviceLoader->loadFile('exporter');
```
