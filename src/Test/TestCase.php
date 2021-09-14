<?php

declare(strict_types=1);

namespace Webf\Flysystem\DsnBundle\Test;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\ResolveTaggedIteratorArgumentPass;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Webf\Flysystem\DsnBundle\DependencyInjection\Configuration;
use Webf\Flysystem\DsnBundle\DependencyInjection\WebfFlysystemDsnExtension;
use Webf\Flysystem\DsnBundle\WebfFlysystemDsnBundle;
use Webf\FlysystemFailoverBundle\DependencyInjection\WebfFlysystemFailoverExtension;
use Webf\FlysystemFailoverBundle\MessageRepository\MessageRepositoryInterface;

/**
 * @internal
 * @coversNothing
 */
class TestCase extends BaseTestCase
{
    protected function createContainer(array $config = []): ContainerBuilder
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
            new ServiceLocatorTagPass(),
            new ResolveTaggedIteratorArgumentPass(),
        ]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->getCompilerPassConfig()->setAfterRemovingPasses([]);

        $bundle = new WebfFlysystemDsnBundle();
        $bundle->build($container);

        $container->set(
            WebfFlysystemFailoverExtension::MESSAGE_REPOSITORY_SERVICE_ID,
            $this->createMock(MessageRepositoryInterface::class)
        );

        return $container;
    }

    protected function createCompiledContainer(array $config = []): ContainerBuilder
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

    protected function assertServiceIsInstanciable(
        string $id,
        ?ContainerBuilder $container = null
    ): void {
        $this->assertServicesAreInstanciable([$id], $container);
    }
}
