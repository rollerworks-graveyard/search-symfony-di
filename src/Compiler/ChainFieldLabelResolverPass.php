<?php

/*
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Extension\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass registers services tagged as 'rollerworks_search.field_label_resolver'
 * on the 'rollerworks_search.field_label_resolver.chain' service.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class ChainFieldLabelResolverPass extends AbstractChainResolverPass
{
    protected function getChainType()
    {
        return 'field_label_resolver';
    }
}
