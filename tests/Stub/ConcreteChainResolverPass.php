<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests\Extension\Symfony\DependencyInjection\Stub;

use Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Compiler\AbstractChainResolverPass;

class ConcreteChainResolverPass extends AbstractChainResolverPass
{
    protected function getChainType()
    {
        return 'concrete_resolver';
    }
}
