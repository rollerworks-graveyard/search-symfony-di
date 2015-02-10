UPGRADE FROM 1.0.0 to 1.0.0-beta2
=================================

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
