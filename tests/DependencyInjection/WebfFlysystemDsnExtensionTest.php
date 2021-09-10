<?php

declare(strict_types=1);

namespace Tests\Webf\Flysystem\DsnBundle\DependencyInjection;

use League\Flysystem\Local\LocalFilesystemAdapter;
use Symfony\Component\DependencyInjection\Definition;
use Webf\Flysystem\Dsn\FlysystemAdapterFactoryInterface;
use Webf\Flysystem\DsnBundle\DependencyInjection\WebfFlysystemDsnExtension;
use Webf\Flysystem\DsnBundle\Flysystem\ServiceAdapterFactory;
use Webf\Flysystem\DsnBundle\Test\TestCase;

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
            WebfFlysystemDsnExtension::SERVICE_ADAPTER_FACTORY_SERVICE_ID,
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

    public function test_service_adapter_factory_receives_tagged_services(): void
    {
        $container = $this->createContainer();

        $container->setDefinition(
            'my_adapter',
            (new Definition(LocalFilesystemAdapter::class))
                ->setArguments([__DIR__])
                ->addTag(WebfFlysystemDsnExtension::ADAPTER_SERVICE_TAG_NAME)
        );

        $serviceLocator = $this->createServiceLocator(['my_adapter', WebfFlysystemDsnExtension::SERVICE_ADAPTER_FACTORY_SERVICE_ID], $container);
        /** @var ServiceAdapterFactory $serviceAdapterFactory */
        $serviceAdapterFactory = $serviceLocator->get(WebfFlysystemDsnExtension::SERVICE_ADAPTER_FACTORY_SERVICE_ID);

        $this->assertSame(
            $serviceLocator->get('my_adapter'),
            $serviceAdapterFactory->createAdapter('service://my_adapter')
        );
    }

    public function test_implementations_of_filesystem_adapter_are_automatically_tagged(): void
    {
        $container = $this->createContainer();

        $container->setDefinition(
            'my_adapter',
            (new Definition(LocalFilesystemAdapter::class))
                ->setArguments([__DIR__])
                ->setAutoconfigured(true)
        );

        $container->compile();

        $this->assertArrayHasKey(
            'my_adapter',
            $container->findTaggedServiceIds(WebfFlysystemDsnExtension::ADAPTER_SERVICE_TAG_NAME)
        );
    }
}
