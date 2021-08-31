<?php

declare(strict_types=1);

namespace Tests\Webf\Flysystem\DsnBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\ResolveTaggedIteratorArgumentPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Webf\Flysystem\Dsn\FlysystemAdapterFactoryInterface;
use Webf\Flysystem\DsnBundle\DependencyInjection\Configuration;
use Webf\Flysystem\DsnBundle\DependencyInjection\WebfFlysystemDsnExtension;

/**
 * @internal
 * @covers \Webf\Flysystem\DsnBundle\DependencyInjection\WebfFlysystemDsnExtension
 */
class WebfFlysystemDsnExtensionTest extends TestCase
{
    public function test_services_are_instanciable()
    {
        $this->assertServicesAreInstanciable([
            WebfFlysystemDsnExtension::ADAPTER_FACTORY_SERVICE_ID,
            WebfFlysystemDsnExtension::AWS_S3_ADAPTER_FACTORY_SERVICE_ID,
            WebfFlysystemDsnExtension::OPENSTACK_SWIFT_ADAPTER_FACTORY_SERVICE_ID,
        ]);
    }

    public function test_interfaces_are_registered_for_autoconfiguration()
    {
        $container = $this->createCompiledContainer();

        $tags = [
            FlysystemAdapterFactoryInterface::class => WebfFlysystemDsnExtension::ADAPTER_FACTORY_TAG_NAME,
        ];

        $autoconfigured = $container->getAutoconfiguredInstanceof();

        foreach ($tags as $class => $tag) {
            $this->assertTrue($autoconfigured[$class]->hasTag($tag));
        }
    }

    public function test_configured_adapter_services_are_registered(): void
    {
        $config = [
            'adapters' => [
                'my_adapter' => 'swift://u:p@h?region=r&container=c',
            ],
        ];

        $this->assertServiceIsInstanciable(
            'webf_flysystem_dsn.adapter.my_adapter',
            $this->createContainer($config)
        );
    }

    private function createContainer(array $config = []): ContainerBuilder
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.environment' => 'test',
        ]));
        $container->registerExtension(new WebfFlysystemDsnExtension());
        $container->loadFromExtension(
            'webf_flysystem_dsn',
            (new Processor())->processConfiguration(new Configuration(), [
                'webf_flysystem_dsn' => $config,
            ])
        );

        $container->getCompilerPassConfig()->setOptimizationPasses([
            new ResolveTaggedIteratorArgumentPass(),
        ]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        return $container;
    }

    private function createCompiledContainer(array $config = []): ContainerBuilder
    {
        $container = $this->createContainer($config);
        $container->compile();

        return $container;
    }

    protected function createServiceLocator(
        array $ids,
        ?ContainerBuilder $container = null
    ): ServiceLocator {
        if (null === $container) {
            $container = $this->createContainer();
        }

        if ($container->isCompiled()) {
            throw new \InvalidArgumentException('$container must not be compiled.');
        }

        $container->setDefinition(
            'service_locator',
            (new Definition(ServiceLocator::class))
                ->setArguments([array_combine(
                    $ids,
                    array_map(
                        function (string $id) {
                            return new ServiceClosureArgument(new Reference($id));
                        },
                        $ids
                    )
                )])
                ->setPublic(true)
        );

        $container->compile();

        try {
            /** @var ServiceLocator $serviceLocator */
            $serviceLocator = $container->get('service_locator');
        } catch (\Exception $e) {
        }

        return $serviceLocator;
    }

    /**
     * @param string[] $ids
     */
    protected function assertServicesAreInstanciable(
        array $ids,
        ?ContainerBuilder $container = null
    ): void {
        if (null === $container) {
            $container = $this->createContainer();
        }

        if ($container->isCompiled()) {
            throw new \InvalidArgumentException('$container must not be compiled.');
        }

        $serviceLocator = $this->createServiceLocator($ids, $container);
        foreach ($ids as $id) {
            try {
                $serviceLocator->get($id);
            } catch (\Throwable $e) {
                $this->fail(sprintf('Service "%s" cannot be instantiated: %s', $id, $e->getMessage()));
            }
        }

        $this->addToAssertionCount(1);
    }

    private function assertServiceIsInstanciable(
        string $id,
        ?ContainerBuilder $container = null
    ): void {
        $this->assertServicesAreInstanciable([$id], $container);
    }
}
