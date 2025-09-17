<?php

declare(strict_types=1);

namespace Tests\Webf\Flysystem\DsnBundle\Flysystem;

use League\Flysystem\FilesystemAdapter;
use Nyholm\Dsn\DsnParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Webf\Flysystem\Dsn\Exception\DsnException;
use Webf\Flysystem\Dsn\Exception\UnableToCreateAdapterException;
use Webf\Flysystem\Dsn\Exception\UnsupportedDsnException;
use Webf\Flysystem\DsnBundle\Flysystem\ServiceAdapterFactory;

/**
 * @internal
 *
 * @covers \Webf\Flysystem\DsnBundle\Flysystem\ServiceAdapterFactory
 */
class ServiceAdapterFactoryTest extends TestCase
{
    public function test_create_adapter(): void
    {
        $service1 = $this->createMock(FilesystemAdapter::class);
        $service2 = $this->createMock(FilesystemAdapter::class);

        $factory = new ServiceAdapterFactory(new ServiceLocator([
            'service1' => fn () => $service1,
            'service2' => fn () => $service2,
        ]));

        $this->assertSame($service1, $factory->createAdapter('service://service1'));
        $this->assertSame($service2, $factory->createAdapter('service://service2'));
        $this->assertNotSame($service1, $service2);
    }

    public function test_create_adapter_throws_exception_when_dsn_is_invalid(): void
    {
        $factory = new ServiceAdapterFactory(new ServiceLocator([]));

        $this->expectException(DsnException::class);

        $factory->createAdapter('Invalid DSN');
    }

    public function test_create_adapter_throws_exception_when_dsn_is_not_supported(): void
    {
        $factory = new ServiceAdapterFactory(new ServiceLocator([]));

        $this->expectException(UnsupportedDsnException::class);

        $factory->createAdapter('adapter://');
    }

    public function test_create_adapter_throws_exception_when_service_does_not_exist(): void
    {
        $factory = new ServiceAdapterFactory(new ServiceLocator([]));

        $this->expectException(UnableToCreateAdapterException::class);

        $factory->createAdapter('service://service1');
    }

    public function test_create_adapter_throws_exception_when_service_does_not_implement_flysystem_adapter_interface(): void
    {
        $service1 = new \stdClass();

        $factory = new ServiceAdapterFactory(new ServiceLocator([
            'service1' => fn () => $service1,
        ]));

        $this->expectException(UnableToCreateAdapterException::class);

        $factory->createAdapter('service://service1');
    }

    public function test_supports(): void
    {
        $factory = new ServiceAdapterFactory(new ServiceLocator([]));

        $this->assertTrue($factory->supports('service://'));
        $this->assertFalse($factory->supports('adapter://'));
        $this->assertFalse($factory->supports('service(inner://)'));
    }

    public function test_supports_throws_exception_when_dsn_is_invalid(): void
    {
        $factory = new ServiceAdapterFactory(new ServiceLocator([]));

        $this->expectException(DsnException::class);

        $factory->supports('Invalid DSN');
    }

    /**
     * @dataProvider get_service_id_data_provider
     */
    public function test_get_service_id(string $dsn, string $expectedServiceId): void
    {
        $this->assertEquals(
            $expectedServiceId,
            ServiceAdapterFactory::getServiceId(DsnParser::parse($dsn))
        );
    }

    public function get_service_id_data_provider(): iterable
    {
        yield 'with host' => ['service://host', 'host'];
        yield 'with path' => ['service:///path', '/path'];
        yield 'with host and path' => ['service://host/path', 'host/path'];
    }
}
