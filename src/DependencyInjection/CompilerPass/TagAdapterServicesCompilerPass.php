<?php

declare(strict_types=1);

namespace Webf\Flysystem\DsnBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Webf\Flysystem\DsnBundle\DependencyInjection\WebfFlysystemDsnExtension;

final class TagAdapterServicesCompilerPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        /**
         * @var array{
         *     adapters: array<
         *         int|string,
         *         array{
         *             dsn: string
         *         }
         *     >
         * } $config
         */
        $config = $container->getParameter(WebfFlysystemDsnExtension::CONFIG_PARAMETER_NAME);

        foreach ($config['adapters'] as $adapter) {
            $matches = [];
            if (preg_match_all('#service://([a-zA-Z0-9_.-\\\\]+)#', $adapter['dsn'], $matches) > 0) {
                foreach ($matches[1] as $serviceId) {
                    if ($container->hasDefinition($serviceId)) {
                        $container
                            ->getDefinition($serviceId)
                            ->addTag(WebfFlysystemDsnExtension::ADAPTER_SERVICE_TAG_NAME)
                        ;
                    }
                }
            }
        }
    }
}
