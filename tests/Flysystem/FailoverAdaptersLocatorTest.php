<?php

declare(strict_types=1);

namespace Tests\Webf\Flysystem\DsnBundle\Flysystem;

use League\Flysystem\FilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Webf\Flysystem\Composite\CompositeFilesystemAdapter;
use Webf\Flysystem\DsnBundle\Flysystem\FailoverAdaptersLocator;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdapter;
use Webf\FlysystemFailoverBundle\MessageRepository\InMemoryMessageRepository;

/**
 * @internal
 *
 * @covers \Webf\Flysystem\DsnBundle\Flysystem\FailoverAdaptersLocator
 */
class FailoverAdaptersLocatorTest extends TestCase
{
    public function test_it_filters_failover_adapters(): void
    {
        $locator = new FailoverAdaptersLocator([
            $this->createMock(FilesystemAdapter::class),
            new FailoverAdapter('adapter1', [], new InMemoryMessageRepository()),
            $this->createMock(FilesystemAdapter::class),
            new FailoverAdapter('adapter2', [], new InMemoryMessageRepository()),
        ]);

        $this->assertEquals(
            ['adapter1', 'adapter2'],
            array_keys(iterator_to_array($locator))
        );
    }

    public function test_it_goes_through_inner_adapters_of_composite_adapters(): void
    {
        $locator = new FailoverAdaptersLocator([
            $this->createCompositeAdapter([
                new FailoverAdapter('adapter1', [], new InMemoryMessageRepository()),
                $this->createMock(FilesystemAdapter::class),
                $this->createCompositeAdapter([
                    $this->createMock(FilesystemAdapter::class),
                    new FailoverAdapter('adapter2', [], new InMemoryMessageRepository()),
                ]),
            ]),
            new FailoverAdapter('adapter3', [], new InMemoryMessageRepository()),
        ]);

        $this->assertEquals(
            ['adapter1', 'adapter2', 'adapter3'],
            array_keys(iterator_to_array($locator))
        );
    }

    private function createCompositeAdapter(array $innerAdapters = []): FilesystemAdapter
    {
        $adapter = $this->createMock(CompositeFilesystemAdapter::class);
        $adapter
            ->method('getInnerAdapters')
            ->willReturn($innerAdapters)
        ;

        return $adapter;
    }
}
