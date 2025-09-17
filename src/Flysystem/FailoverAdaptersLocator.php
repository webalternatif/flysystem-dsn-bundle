<?php

declare(strict_types=1);

namespace Webf\Flysystem\DsnBundle\Flysystem;

use League\Flysystem\FilesystemAdapter;
use Webf\Flysystem\Composite\CompositeFilesystemAdapter;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdapter;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdaptersLocator as BaseFailoverAdaptersLocator;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdaptersLocatorInterface;

/**
 * @template-implements FailoverAdaptersLocatorInterface<FilesystemAdapter>
 * @template-implements \IteratorAggregate<string, FailoverAdapter<FilesystemAdapter>>
 */
final readonly class FailoverAdaptersLocator implements FailoverAdaptersLocatorInterface, \IteratorAggregate
{
    private BaseFailoverAdaptersLocator $baseLocator;

    /**
     * @param iterable<FilesystemAdapter> $adapters
     */
    public function __construct(iterable $adapters)
    {
        $this->baseLocator = new BaseFailoverAdaptersLocator($this->getFailoverAdapters($adapters));
    }

    /**
     * @param iterable<FilesystemAdapter> $adapters
     *
     * @return iterable<FailoverAdapter>
     */
    private function getFailoverAdapters(iterable $adapters): iterable
    {
        foreach ($adapters as $adapter) {
            if ($adapter instanceof FailoverAdapter) {
                yield $adapter;
            }

            if ($adapter instanceof CompositeFilesystemAdapter) {
                yield from $this->getFailoverAdapters($adapter->getInnerAdapters());
            }
        }
    }

    #[\Override]
    public function get(string $name): FailoverAdapter
    {
        return $this->baseLocator->get($name);
    }

    #[\Override]
    public function getIterator(): \Traversable
    {
        return $this->baseLocator->getIterator();
    }
}
