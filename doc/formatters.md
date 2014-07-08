Formatters
==========

You can always register a formatter as service, tagging the service
as 'rollerworks_search.formatter' ensures it will be registered at
the 'rollerworks_search.chain_formatter' service.

**Note:** Unless you give a 'priority' to the formatter,
the priority defaults to 0 (last).

```php
$container
    ->register('rollerworks_search.formatter.transformer', 'Rollerworks\Component\Search\Formatter\TransformFormatter')
    ->addTag('rollerworks_search.formatter', array('priority' => 1000));
```

## Loading bundled formatters

The RollerworksSearch package already provides some formatters
for transforming, validating and optimizing.

And also performing these in sequence using the ChainFormatter.

You can load these formatters (in correct order) by importing the
'formatter.xml' service-definition file from this package.

```php
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\ServiceLoader;

$serviceLoader = new ServiceLoader($container);
$serviceLoader->loadFile('services'); // this ensures the 'rollerworks_search.chain_formatter' service is registered
$serviceLoader->loadFile('formatter');
```

