<?php

declare(strict_types=1);

namespace Tests\Webf\Flysystem\DsnBundle;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Webf\Flysystem\DsnBundle\WebfFlysystemDsnBundle;

/**
 * @internal
 *
 * @covers \Webf\Flysystem\DsnBundle\WebfFlysystemDsnBundle
 */
class WebfFlysystemDsnBundleTest extends TestCase
{
    public function test_all_compiler_passes_are_added_to_container_builder()
    {
        $container = new ContainerBuilder();

        $bundle = new WebfFlysystemDsnBundle();
        $bundle->build($container);

        $compilerPasses = array_map(
            'get_class',
            $container->getCompilerPassConfig()->getPasses()
        );

        $finder = new Finder();
        $finder->files()->in(__DIR__.'/../src/DependencyInjection/CompilerPass');

        foreach ($finder as $file) {
            $this->assertContains(
                sprintf(
                    'Webf\\Flysystem\\DsnBundle\\DependencyInjection\\CompilerPass\\%s',
                    $file->getFilenameWithoutExtension()
                ),
                $compilerPasses,
                sprintf(
                    'Compiler pass "%s" is not added to container builder.',
                    $file->getFilenameWithoutExtension()
                )
            );
        }
    }
}
