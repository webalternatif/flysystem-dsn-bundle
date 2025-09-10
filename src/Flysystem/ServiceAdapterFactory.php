<?php

declare(strict_types=1);

namespace Webf\Flysystem\DsnBundle\Flysystem;

use League\Flysystem\FilesystemAdapter;
use Nyholm\Dsn\Configuration\Dsn;
use Nyholm\Dsn\DsnParser;
use Nyholm\Dsn\Exception\FunctionsNotAllowedException;
use Nyholm\Dsn\Exception\InvalidDsnException as NyholmInvalidDsnException;
use Psr\Container\ContainerInterface;
use Webf\Flysystem\Dsn\Exception\InvalidDsnException;
use Webf\Flysystem\Dsn\Exception\UnableToCreateAdapterException;
use Webf\Flysystem\Dsn\Exception\UnsupportedDsnException;
use Webf\Flysystem\Dsn\FlysystemAdapterFactoryInterface;
use Webf\Flysystem\DsnBundle\DependencyInjection\WebfFlysystemDsnExtension;

final class ServiceAdapterFactory implements FlysystemAdapterFactoryInterface
{
    public function __construct(private ContainerInterface $serviceLocator)
    {
    }

    #[\Override]
    public function createAdapter(string $dsn): FilesystemAdapter
    {
        $dsnString = $dsn;
        try {
            $dsn = DsnParser::parse($dsn);
        } catch (NyholmInvalidDsnException $e) {
            throw new InvalidDsnException($e->getMessage(), previous: $e);
        }

        if ('service' !== $dsn->getScheme()) {
            throw UnsupportedDsnException::create($this, $dsnString);
        }

        $serviceId = self::getServiceId($dsn);
        try {
            $adapter = $this->serviceLocator->get($serviceId);
        } catch (\Throwable) {
            throw UnableToCreateAdapterException::create(sprintf('Service "%s" must be tagged with "%s"', $serviceId, WebfFlysystemDsnExtension::ADAPTER_SERVICE_TAG_NAME), $dsnString);
        }

        if (!$adapter instanceof FilesystemAdapter) {
            throw UnableToCreateAdapterException::create(sprintf('Service "%s" does not implement "%s"', $serviceId, FilesystemAdapter::class), $dsnString);
        }

        return $adapter;
    }

    #[\Override]
    public function supports(string $dsn): bool
    {
        try {
            $scheme = DsnParser::parse($dsn)->getScheme() ?? '';
        } catch (FunctionsNotAllowedException) {
            return false;
        } catch (NyholmInvalidDsnException $e) {
            throw new InvalidDsnException($e->getMessage(), previous: $e);
        }

        return 'service' === $scheme;
    }

    public static function getServiceId(Dsn $dsn): string
    {
        return ($dsn->getHost() ?? '').($dsn->getPath() ?? '');
    }
}
