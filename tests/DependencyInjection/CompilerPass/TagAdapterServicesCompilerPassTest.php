<?php

declare(strict_types=1);

namespace Tests\Webf\Flysystem\DsnBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Definition;
use Webf\Flysystem\DsnBundle\DependencyInjection\WebfFlysystemDsnExtension;
use Webf\Flysystem\DsnBundle\Test\TestCase;

/**
 * @internal
 * @covers \Webf\Flysystem\DsnBundle\DependencyInjection\CompilerPass\TagAdapterServicesCompilerPass
 */
class TagAdapterServicesCompilerPassTest extends TestCase
{
    public function test_all_services_in_dsn_are_tagged(): void
    {
        $config = [
            'adapters' => [
                'simple' => 'service://service1',
                'function' => 'func(service://service2 service://service3)',
                'nested' => 'func(service://service4 func(service://service5))',
            ],
        ];

        $services = ['service1', 'service2', 'service3', 'service4', 'service5'];

        $container = $this->createContainer($config);

        foreach ($services as $service) {
            $container->setDefinition($service, new Definition(\stdClass::class));
        }

        $container->compile();

        foreach ($services as $service) {
            $this->assertTrue(
                $container->getDefinition($service)
                    ->hasTag(WebfFlysystemDsnExtension::ADAPTER_SERVICE_TAG_NAME)
            );
        }
    }
}
