Field types
===========

Field-types must be tagged as `rollerworks_search.type`,
with "alias" as additional parameter.

**Caution:** Alias must equal the name returned by `getName()`.

```php
$container
    ->register('rollerworks_search.type.date', 'Rollerworks\Component\Search\Type\DateType')
    ->addTag('rollerworks_search.type', ['alias' => 'date']);
```

## Field type extensions

Field-type extensions must be tagged as `rollerworks_search.type_extension`,
with "alias" as additional parameter.

Alias defines on which the field-type the extension is applied.

```php
// First register the type. only for explicitness, type can also be registered later
$container
    ->register('rollerworks_search.type.date', 'Rollerworks\Component\Search\Type\DateType')
    ->addTag('rollerworks_search.type', ['alias' => 'date']);

// Now register the type-extension
$container
    ->register(
        'rollerworks_search.type_extension.date_microtime',
        'Rollerworks\Component\Search\Type\DateMicrotimeExtension'
    )
    ->addTag('rollerworks_search.type_extension', ['alias' => 'date']);
```

## Loading bundled types and extensions

The RollerworksSearch package already provides a ridge set of FieldTypes
and extensions. Normally you would load these by enabling the `CoreExtension`,
but then they aren't loaded lazily.

You can register these types (for lazy loading) by importing the
'type.xml' service-definition file from this package.

**Caution:** You must either register them using the `CoreExtension`
**or** by importing them. Don't use both ways, as this breaks the lazy loading.

```php
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\ServiceLoader;

$serviceLoader = new ServiceLoader($container);
$serviceLoader->loadFile('services'); // this ensures the correct services are registered
$serviceLoader->loadFile('type');
```
