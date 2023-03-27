<?php

declare(strict_types=1);

namespace Tests\Webf\Flysystem\DsnBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Webf\Flysystem\DsnBundle\DependencyInjection\Configuration;
use Webf\Flysystem\DsnBundle\DependencyInjection\WebfFlysystemDsnExtension;

/**
 * @internal
 *
 * @covers \Webf\Flysystem\DsnBundle\DependencyInjection\Configuration
 */
class ConfigurationTest extends TestCase
{
    /**
     * @dataProvider configuration_data_provider
     */
    public function test_configuration(array $given, array $expected): void
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.environment' => 'test',
        ]));
        $container->registerExtension(new WebfFlysystemDsnExtension());
        $container->loadFromExtension(
            'webf_flysystem_dsn',
            (new Processor())->processConfiguration(new Configuration(), [
                'webf_flysystem_dsn' => $given,
            ])
        );

        $this->assertEquals(
            [$expected],
            $container->getExtensionConfig('webf_flysystem_dsn')
        );
    }

    public function configuration_data_provider(): iterable
    {
        yield 'empty' => [
            [],
            ['adapters' => []],
        ];

        yield 'single adapter as string' => [
            ['adapters' => ['my_adapter' => 'my_dsn']],
            ['adapters' => ['my_adapter' => ['dsn' => 'my_dsn']]],
        ];

        yield 'single adapter as array' => [
            ['adapters' => ['my_adapter' => ['dsn' => 'my_dsn']]],
            ['adapters' => ['my_adapter' => ['dsn' => 'my_dsn']]],
        ];

        yield 'multiple adapter as string' => [
            ['adapters' => [
                'my_adapter_1' => 'my_dsn_1',
                'my_adapter_2' => 'my_dsn_2',
            ]],
            ['adapters' => [
                'my_adapter_1' => ['dsn' => 'my_dsn_1'],
                'my_adapter_2' => ['dsn' => 'my_dsn_2'],
            ]],
        ];

        yield 'multiple adapter as array' => [
            ['adapters' => [
                'my_adapter_1' => ['dsn' => 'my_dsn_1'],
                'my_adapter_2' => ['dsn' => 'my_dsn_2'],
            ]],
            ['adapters' => [
                'my_adapter_1' => ['dsn' => 'my_dsn_1'],
                'my_adapter_2' => ['dsn' => 'my_dsn_2'],
            ]],
        ];
    }
}
