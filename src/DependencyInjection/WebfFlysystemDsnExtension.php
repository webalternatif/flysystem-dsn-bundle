<?php

declare(strict_types=1);

namespace Webf\Flysystem\DsnBundle\DependencyInjection;

use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\PhpseclibV2\SftpAdapter;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Webf\Flysystem\Dsn\AwsS3AdapterFactory;
use Webf\Flysystem\Dsn\FailoverAdapterFactory;
use Webf\Flysystem\Dsn\FlysystemAdapterFactory;
use Webf\Flysystem\Dsn\FlysystemAdapterFactoryInterface;
use Webf\Flysystem\Dsn\InMemoryAdapterFactory;
use Webf\Flysystem\Dsn\LocalAdapterFactory;
use Webf\Flysystem\Dsn\OpenStackSwiftAdapterFactory;
use Webf\Flysystem\Dsn\SftpAdapterFactory;
use Webf\Flysystem\DsnBundle\Flysystem\ServiceAdapterFactory;
use Webf\Flysystem\OpenStackSwift\OpenStackSwiftAdapter;
use Webf\FlysystemFailoverBundle\DependencyInjection\WebfFlysystemFailoverExtension;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdapter;

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
    public const CONFIG_PARAMETER_NAME = self::PREFIX . '.config';

    public const ADAPTER_SERVICE_ID_PREFIX = self::PREFIX . '.adapter';

    public const ADAPTER_FACTORY_SERVICE_ID = self::PREFIX . '.adapter_factory';
    public const AWS_S3_ADAPTER_FACTORY_SERVICE_ID =
        self::PREFIX . '.adapter_factory.s3';
    public const FAILOVER_ADAPTER_FACTORY_SERVICE_ID =
        self::PREFIX . '.adapter_factory.failover';
    public const IN_MEMORY_ADAPTER_FACTORY_SERVICE_ID =
        self::PREFIX . '.adapter_factory.in_memory';
    public const LOCAL_ADAPTER_FACTORY_SERVICE_ID =
        self::PREFIX . '.adapter_factory.local';
    public const OPENSTACK_SWIFT_ADAPTER_FACTORY_SERVICE_ID =
        self::PREFIX . '.adapter_factory.swift';
    public const SFTP_ADAPTER_FACTORY_SERVICE_ID =
        self::PREFIX . '.adapter_factory.sftp';
    public const SERVICE_ADAPTER_FACTORY_SERVICE_ID =
        self::PREFIX . '.adapter_factory.service';

    public const ADAPTER_TAG_NAME = self::PREFIX . '.adapter'; // Tag for every adapter configured by this bundle
    public const ADAPTER_FACTORY_TAG_NAME = self::PREFIX . '.adapter_factory';
    public const ADAPTER_SERVICE_TAG_NAME = self::PREFIX . '.adapter_service'; // Tag for adapters referenced as "service://<service_id>" in DSNs

    public function load(array $configs, ContainerBuilder $container): void
    {
        /** @var _Config $config */
        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter(self::CONFIG_PARAMETER_NAME, $config);

        $this->registerFactories($container);
        $this->registerAdapters($container, $config);
    }

    private function registerFactories(ContainerBuilder $container): void
    {
        $container->setDefinition(
            self::ADAPTER_FACTORY_SERVICE_ID,
            (new Definition(FlysystemAdapterFactory::class))
                ->setArguments([
                    new TaggedIteratorArgument(self::ADAPTER_FACTORY_TAG_NAME),
                ])
        );

        $container->setAlias(
            FlysystemAdapterFactoryInterface::class,
            self::ADAPTER_FACTORY_SERVICE_ID
        );

        if (class_exists(AwsS3V3Adapter::class)) {
            $container->setDefinition(
                self::AWS_S3_ADAPTER_FACTORY_SERVICE_ID,
                (new Definition(AwsS3AdapterFactory::class))
                    ->addTag(self::ADAPTER_FACTORY_TAG_NAME)
            );
        }

        if (class_exists(FailoverAdapter::class)) {
            $container->setDefinition(
                self::FAILOVER_ADAPTER_FACTORY_SERVICE_ID,
                (new Definition(FailoverAdapterFactory::class))
                    ->setArguments([
                        new Reference(self::ADAPTER_FACTORY_SERVICE_ID),
                        new Reference(WebfFlysystemFailoverExtension::MESSAGE_REPOSITORY_SERVICE_ID),
                    ])
                    ->addTag(self::ADAPTER_FACTORY_TAG_NAME)
            );
        }

        if (class_exists(InMemoryFilesystemAdapter::class)) {
            $container->setDefinition(
                self::IN_MEMORY_ADAPTER_FACTORY_SERVICE_ID,
                (new Definition(InMemoryAdapterFactory::class))
                    ->addTag(self::ADAPTER_FACTORY_TAG_NAME)
            );
        }

        if (class_exists(LocalFilesystemAdapter::class)) {
            $container->setDefinition(
                self::LOCAL_ADAPTER_FACTORY_SERVICE_ID,
                (new Definition(LocalAdapterFactory::class))
                    ->addTag(self::ADAPTER_FACTORY_TAG_NAME)
            );
        }

        if (class_exists(OpenStackSwiftAdapter::class)) {
            $container->setDefinition(
                self::OPENSTACK_SWIFT_ADAPTER_FACTORY_SERVICE_ID,
                (new Definition(OpenStackSwiftAdapterFactory::class))
                    ->addTag(self::ADAPTER_FACTORY_TAG_NAME)
            );
        }

        if (class_exists(SftpAdapter::class)) {
            $container->setDefinition(
                self::SFTP_ADAPTER_FACTORY_SERVICE_ID,
                (new Definition(SftpAdapterFactory::class))
                    ->addTag(self::ADAPTER_FACTORY_TAG_NAME)
            );
        }

        $container->setDefinition(
            self::SERVICE_ADAPTER_FACTORY_SERVICE_ID,
            (new Definition(ServiceAdapterFactory::class))
                ->setArguments([
                    new ServiceLocatorArgument(
                        new TaggedIteratorArgument(self::ADAPTER_SERVICE_TAG_NAME, null, null, true)
                    ),
                ])
                ->addTag(self::ADAPTER_FACTORY_TAG_NAME)
        );

        $container->registerForAutoconfiguration(FlysystemAdapterFactoryInterface::class)
            ->addTag(self::ADAPTER_FACTORY_TAG_NAME)
        ;

        $container->registerForAutoconfiguration(FilesystemAdapter::class)
            ->addTag(self::ADAPTER_SERVICE_TAG_NAME)
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
                    ->addTag(self::ADAPTER_TAG_NAME)
            );
        }
    }
}
