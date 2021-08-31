<?php

declare(strict_types=1);

namespace Webf\Flysystem\DsnBundle\DependencyInjection;

use League\Flysystem\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Webf\Flysystem\Dsn\AwsS3AdapterFactory;
use Webf\Flysystem\Dsn\FlysystemAdapterFactory;
use Webf\Flysystem\Dsn\FlysystemAdapterFactoryInterface;
use Webf\Flysystem\Dsn\OpenStackSwiftAdapterFactory;

/**
 * @psalm-type _Config=array{
 *     adapters: array<
 *         int|string,
 *         array{
 *             dsn: string
 *         }
 *     >
 * }
 */
class WebfFlysystemDsnExtension extends Extension
{
    private const PREFIX = 'webf_flysystem_dsn';

    public const ADAPTER_SERVICE_ID_PREFIX = self::PREFIX . '.adapter';

    public const ADAPTER_FACTORY_SERVICE_ID = self::PREFIX . '.adapter_factory';
    public const AWS_S3_ADAPTER_FACTORY_SERVICE_ID = self::PREFIX . '.adapter_factory.s3';
    public const OPENSTACK_SWIFT_ADAPTER_FACTORY_SERVICE_ID = self::PREFIX . '.adapter_factory.swift';

    public const ADAPTER_FACTORY_TAG_NAME = self::PREFIX . '.adapter_factory';

    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var _Config $config */
        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->registerServices($container);
        $this->registerAdapters($container, $config);
    }

    private function registerServices(ContainerBuilder $container): void
    {
        $container->setDefinition(
            self::ADAPTER_FACTORY_SERVICE_ID,
            (new Definition(FlysystemAdapterFactory::class))
                ->setArguments([
                    new TaggedIteratorArgument(self::ADAPTER_FACTORY_TAG_NAME),
                ])
        );

        $container->setDefinition(
            self::AWS_S3_ADAPTER_FACTORY_SERVICE_ID,
            (new Definition(AwsS3AdapterFactory::class))
                ->addTag(self::ADAPTER_FACTORY_TAG_NAME)
        );

        $container->setDefinition(
            self::OPENSTACK_SWIFT_ADAPTER_FACTORY_SERVICE_ID,
            (new Definition(OpenStackSwiftAdapterFactory::class))
                ->addTag(self::ADAPTER_FACTORY_TAG_NAME)
        );

        $container->registerForAutoconfiguration(FlysystemAdapterFactoryInterface::class)
            ->addTag(self::ADAPTER_FACTORY_TAG_NAME)
        ;
    }

    /**
     * @param _Config $config
     */
    private function registerAdapters(
        ContainerBuilder $container,
        array $config
    ): void {
        foreach ($config['adapters'] as $name => $adapter) {
            $container->setDefinition(
                sprintf('%s.%s', self::ADAPTER_SERVICE_ID_PREFIX, $name),
                (new Definition(FilesystemAdapter::class))
                    ->setFactory([
                        new Reference(self::ADAPTER_FACTORY_SERVICE_ID),
                        'createAdapter',
                    ])
                    ->setArguments([$adapter['dsn']])
            );
        }
    }
}
