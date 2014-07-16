FieldSet
========

As you properly know, a Fieldset can be generated at any given time.
But its also possible to register FieldSets in the Service Container.

Registering doesn't really add any speed benefits, but it does make
loading them a bit easier.

FieldSets in the service-container can be lazy loaded using the
`rollerworks_search.fieldset_registry` service.

**Note:** FieldSets are marked immutable, and can not change after
being registered!

## Registering

Registering a FieldSet is done using the `Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory\FieldSetFactory`
class.

**Caution:** The FieldSetFactory uses the FieldSet provides by this extension,
not the FieldSet provided by the core package.

The following example registers a `Rollerworks\Component\Search\FieldSet`
service with service-id `rollerworks_search.fieldset.acme_users`.

```php

use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory\FieldSetFactory;
use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory\FieldSet;
use Symfony\Component\DependencyInjection\ContainerBuilder;

$container = new ContainerBuilder();

$fieldSetFactory = new FieldSetFactory($container);

$fieldSet = new FieldSet('acme_users');

// set($name, $type = null, $modelClass = null, $modelProperty = null, $required = false, array $options = array())
$fieldSet->set('id', 'integer');

$fieldSetFactory->register($fieldSet);
```

After compiling the container, you can get the FieldSet using the
'rollerworks_search.fieldset_registry' service.

```php
$fieldSet = $container->get('rollerworks_search.fieldset_registry')->getFieldSet('acme_users');
```

## FieldSetBuilder

See the `Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Factory\FieldSetBuilder`
class for code and details.

TODO...
