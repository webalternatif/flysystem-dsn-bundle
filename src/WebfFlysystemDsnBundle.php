<?php

declare(strict_types=1);

namespace Webf\Flysystem\DsnBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Webf\Flysystem\DsnBundle\DependencyInjection\CompilerPass\ReplaceFailoverAdaptersLocatorCompilerPass;
use Webf\Flysystem\DsnBundle\DependencyInjection\CompilerPass\TagAdapterServicesCompilerPass;

class WebfFlysystemDsnBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ReplaceFailoverAdaptersLocatorCompilerPass());
        $container->addCompilerPass(new TagAdapterServicesCompilerPass());
    }
}
