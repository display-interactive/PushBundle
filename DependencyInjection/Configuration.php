<?php

namespace Display\PushBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('display_push');

        $rootNode
            ->children()
                ->scalarNode('entity_manager')
                    ->defaultValue('default')
                    ->info('entity manager used by the PushManager')
                ->end()
                ->scalarNode('translation_domain')
                    ->defaultValue(null)
                    ->info('default translation domain for message')
                ->end()
            ->end()
        ;


        return $treeBuilder;
    }
}
