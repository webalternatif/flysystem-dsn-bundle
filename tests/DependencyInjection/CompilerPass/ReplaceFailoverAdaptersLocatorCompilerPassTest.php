<?php

declare(strict_types=1);

namespace Tests\Webf\Flysystem\DsnBundle\DependencyInjection\CompilerPass;

use League\Flysystem\FilesystemAdapter;
use Symfony\Component\Config\Definition\Processor;
use Webf\Flysystem\DsnBundle\Test\TestCase;
use Webf\FlysystemFailoverBundle\DependencyInjection\Configuration;
use Webf\FlysystemFailoverBundle\DependencyInjection\WebfFlysystemFailoverExtension;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdapter;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdaptersLocatorInterface;

/**
 * @internal
 *
 * @covers \Webf\Flysystem\DsnBundle\DependencyInjection\CompilerPass\ReplaceFailoverAdaptersLocatorCompilerPass
 */
class ReplaceFailoverAdaptersLocatorCompilerPassTest extends TestCase
{
    public function test_failover_dsn_adapters_are_injected_in_locator(): void
    {
        $serviceId = WebfFlysystemFailoverExtension::FAILOVER_ADAPTERS_LOCATOR_SERVICE_ID;

        $container = $this->createContainer([
            'adapters' => [
                'default' => 'failover(swift://u:p@h?region=r&container=c swift://u:p@h?region=r&container=c)?name=my_adapter',
            ],
        ]);

        $container->registerExtension(new WebfFlysystemFailoverExtension());
        $container->loadFromExtension('webf_flysystem_failover');

        $serviceLocator = $this->createServiceLocator([$serviceId], $container);
        /** @var FailoverAdaptersLocatorInterface $failoverAdaptersLocator */
        $failoverAdaptersLocator = $serviceLocator->get($serviceId);

        $this->assertInstanceOf(
            FailoverAdapter::class,
            $failoverAdaptersLocator->get('my_adapter')
        );
    }

    public function test_original_failover_adapters_are_injected_in_locator(): void
    {
        $serviceId = WebfFlysystemFailoverExtension::FAILOVER_ADAPTERS_LOCATOR_SERVICE_ID;

        $container = $this->createContainer();

        $container->registerExtension(new WebfFlysystemFailoverExtension());
        $container->loadFromExtension(
            'webf_flysystem_failover',
            (new Processor())->processConfiguration(new Configuration(), [
                'webf_flysystem_failover' => [
                    'adapters' => [
                        'default' => [
                            'adapters' => ['adapter1', 'adapter2'],
                        ],
                    ],
                ],
            ])
        );

        $container->set('adapter1', $this->createMock(FilesystemAdapter::class));
        $container->set('adapter2', $this->createMock(FilesystemAdapter::class));

        $serviceLocator = $this->createServiceLocator([$serviceId], $container);
        /** @var FailoverAdaptersLocatorInterface $failoverAdaptersLocator */
        $failoverAdaptersLocator = $serviceLocator->get($serviceId);

        $this->assertInstanceOf(
            FailoverAdapter::class,
            $failoverAdaptersLocator->get('default')
        );
    }
}
