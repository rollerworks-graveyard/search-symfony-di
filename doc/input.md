InputProcessor
==============

## Registering

Input processors must be tagged as `rollerworks_search.input_processor`,
with "alias" as additional parameter.

**Note:** Alias is a shortcut for getting the input processor, else the
service-id is used instead.

```php
$container
    ->register('rollerworks_search.input_processor.filter_query', 'Rollerworks\Component\Search\Input\FilterQuery')
    ->addTag('rollerworks_search.input_processor', array('alias' => 'filter_query'));
```

**Caution:** To ensure a new input processor is returned every time, the services
tagged as input processor will be updated with scope prototype.

## Loading

To get a new input processor, use the `rollerworks_search.input_factory` service
which returns a new `Rollerworks\Component\Search\InputProcessorInterface` instance.

```php
// create() expects the exporter alias or service-id
$exporter = $container->get('rollerworks_search.exporter_factory')->create('filter_query');
```
