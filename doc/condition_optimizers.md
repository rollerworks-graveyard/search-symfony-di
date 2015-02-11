ConditionOptimizers
===================

You can register a condition-optimizer as service, tagging the service
as 'rollerworks_search.condition_optimizer' ensures it will be registered
at the 'rollerworks_search.chain_condition' service.

```php
$container
    ->register(
        'rollerworks_search.condition_optimizer.duplicate_remove',
        'Rollerworks\Component\Search\ConditionOptimizer\DuplicateRemover'
    )
    ->addTag('rollerworks_search.condition_optimizer');
```

## Loading bundled condition-optimizers

The RollerworksSearch package already provides some condition-optimizers
for removing duplicate values, overlapping ranges and more.

**Note:** The bundles condition-optimizers are marked private services.

And also performing these in the correct order using the ChainOptimizer.

You can load these optimizers by importing the
'condition_optimizers.xml' service-definition file from this package.

```php
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\ServiceLoader;

$serviceLoader = new ServiceLoader($container);
$serviceLoader->loadFile('services'); // this ensures the required core services are registered
$serviceLoader->loadFile('condition_optimizers');
```

