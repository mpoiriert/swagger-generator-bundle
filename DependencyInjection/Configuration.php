<?php

namespace Draw\SwaggerGeneratorBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('draw_swagger_generator');

        $rootNode->children()
            ->arrayNode('twig_extension')
                ->children()
                    ->arrayNode('registry')
                        ->prototype('variable')->end()
                    ->end()
                    ->arrayNode('filters')
                        ->useAttributeAsKey('name')
                        ->prototype('variable')->end()
                    ->end()
                    ->arrayNode('php_functions')
                        ->prototype('array')
                            ->children()
                                ->integerNode('argumentPosition')->defaultValue(0)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}