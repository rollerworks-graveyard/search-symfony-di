UPGRADE
=======

This document contains all the instructions for upgrading to the latest
version of this package. Note that RollerworksSearch has it's own UPGRADE
file with further instructions about the core package.

UPGRADE FROM1.0.0-beta2 to 1.0.0-beta3
--------------------------------------

* This version is compatible with RollerworksSearch v1.0.0-beta5 and up.

* Core service files "services" and "type" are now loaded when initializing
  the `Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\ServiceLoader`.

  Before:

  ```php
  $serviceLoader = new ServiceLoader();
  $serviceLoader->loadFile('services');
  $serviceLoader->loadFile('type');
  ```
  
  After:
  
  ```php
  $serviceLoader = new ServiceLoader();
  $serviceLoader->loadFile('services');
  $serviceLoader->loadFile('type');
  ```
  
* The `Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\FieldSetRegistry`
  now implements the `Rollerworks\Component\Search\FieldSetRegistryInterface`.
  
  The `getFieldSet()` method is renamed to `get()`, which is provided by
  the `FieldSetRegistryInterface`.
  
* The `rollerworks_search.alias_resolver.chain` service is renamed to
  `rollerworks_search.field_alias_resolver.chain`.

UPGRADE FROM 1.0.0 to 1.0.0-beta2
---------------------------------

The 'Symfony DependencyInjection Component' extension for RollerworksSearch
is updated to reflect the changes done in the RollerworksSearch core package.

## ConditionOptimizers (former Formatters)

* The `formatter` service file is renamed to `condition_optimizers`.
      
    Before:

    ```php
    $serviceLoader->loadFile('formatter');
    ```

    After:

    ```php
    $serviceLoader->loadFile('condition_optimizers');
    ```
    
* The `Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Compiler\FormatterPass`
  is renamed to `Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Compiler\ConditionOptimizerPass`.

## Input processor

Input processors are reusable now, meaning that the `InputFactory`
returns the same instance now. And that processors are not changed to scope
prototype anymore.
