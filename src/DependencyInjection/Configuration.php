<?php

declare(strict_types=1);

namespace Webf\Flysystem\DsnBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    #[\Override]
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('webf_flysystem_dsn');
        $rootNode = $treeBuilder->getRootNode();

        $this->addAdaptersSection($rootNode);

        return $treeBuilder;
    }

    private function addAdaptersSection(ArrayNodeDefinition $rootNode): void
    {
        $rootNodeChildren = $rootNode
            ->fixXmlConfig('adapter')
            ->children()
        ;

        $adaptersPrototype = $rootNodeChildren
            ->arrayNode('adapters')
            ->info(sprintf(
                'Adapter services that will be defined as "%s.{name}".',
                WebfFlysystemDsnExtension::ADAPTER_SERVICE_ID_PREFIX
            ))
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->info('Can be a string to only specify "dsn".')
        ;

        $adaptersPrototype
            ->beforeNormalization()
            ->ifString()
            ->then(function ($v) { return ['dsn' => $v]; })
        ;

        $adaptersPrototype
            ->children()
            ->scalarNode('name')->end()
            ->scalarNode('dsn')->end()
        ;
    }
}
