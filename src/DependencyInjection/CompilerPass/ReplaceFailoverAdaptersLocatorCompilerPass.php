<?php

declare(strict_types=1);

namespace Webf\Flysystem\DsnBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Webf\Flysystem\DsnBundle\DependencyInjection\WebfFlysystemDsnExtension;
use Webf\Flysystem\DsnBundle\Flysystem\FailoverAdaptersLocator;
use Webf\FlysystemFailoverBundle\DependencyInjection\WebfFlysystemFailoverExtension;

final class ReplaceFailoverAdaptersLocatorCompilerPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        if (!class_exists(WebfFlysystemFailoverExtension::class)) {
            return;
        }

        $serviceId = WebfFlysystemFailoverExtension::FAILOVER_ADAPTERS_LOCATOR_SERVICE_ID;
        $tagName = WebfFlysystemDsnExtension::ADAPTER_TAG_NAME;

        if ($container->hasDefinition($serviceId)) {
            $definition = $container->getDefinition($serviceId);

            /** @var IteratorArgument $failoverAdapters */
            $failoverAdapters = $definition->getArgument(0);

            /** @var Reference $reference */
            foreach ($failoverAdapters->getValues() as $reference) {
                $container
                    ->getDefinition($reference->__toString())
                    ->addTag($tagName)
                ;
            }

            $definition
                ->setClass(FailoverAdaptersLocator::class)
                ->setArguments([
                    new TaggedIteratorArgument($tagName),
                ])
            ;
        }
    }
}
