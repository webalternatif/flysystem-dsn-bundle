<?php

declare(strict_types=1);

namespace Webf\Flysystem\DsnBundle\Flysystem;

use League\Flysystem\FilesystemAdapter;
use Webf\Flysystem\Composite\CompositeFilesystemAdapter;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdapter;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdaptersLocator as BaseFailoverAdaptersLocator;

/**
 * @template-extends BaseFailoverAdaptersLocator<FilesystemAdapter>
 */
class FailoverAdaptersLocator extends BaseFailoverAdaptersLocator
{
    /**
     * @param iterable<FilesystemAdapter> $adapters
     */
    public function __construct(iterable $adapters)
    {
        parent::__construct($this->getFailoverAdapters($adapters));
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
}
