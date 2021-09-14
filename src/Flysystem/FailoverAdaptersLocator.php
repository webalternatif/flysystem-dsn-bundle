<?php

declare(strict_types=1);

namespace Webf\Flysystem\DsnBundle\Flysystem;

use League\Flysystem\FilesystemAdapter;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdapter;
use Webf\FlysystemFailoverBundle\Flysystem\FailoverAdaptersLocator as BaseFailoverAdaptersLocator;

class FailoverAdaptersLocator extends BaseFailoverAdaptersLocator
{
    /**
     * @param iterable<FilesystemAdapter> $adapters
     */
    public function __construct(private iterable $adapters)
    {
        parent::__construct($this->getFailoverAdapters());
    }

    /**
     * @return iterable<FailoverAdapter>
     */
    private function getFailoverAdapters(): iterable
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter instanceof FailoverAdapter) {
                yield $adapter;
            }
        }
    }
}
