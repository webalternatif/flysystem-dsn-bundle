<?php

declare(strict_types=1);

namespace Tests\Webf\Flysystem\DsnBundle\Flysystem;

use League\Flysystem\FilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Webf\Flysystem\DsnBundle\Flysystem\FailoverAdaptersLocator;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdapter;

/**
 * @internal
 * @covers \Webf\Flysystem\DsnBundle\Flysystem\FailoverAdaptersLocator
 */
class FailoverAdaptersLocatorTest extends TestCase
{
    public function test_it_filters_failover_adapters(): void
    {
        $locator = new FailoverAdaptersLocator([
            $this->createMock(FilesystemAdapter::class),
            $this->createFailoverAdapter('adapter1'),
            $this->createMock(FilesystemAdapter::class),
            $this->createFailoverAdapter('adapter2'),
        ]);

        $this->assertEquals(
            ['adapter1', 'adapter2'],
            array_keys(iterator_to_array($locator))
        );
    }

    private function createFailoverAdapter(string $name): FilesystemAdapter
    {
        $adapter = $this->createMock(FailoverAdapter::class);
        $adapter
            ->method('getName')
            ->willReturn($name)
        ;

        return $adapter;
    }
}
