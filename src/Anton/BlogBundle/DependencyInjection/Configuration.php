<?php

namespace App\Anton\BlogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('anton_blog');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('limit')
                    ->children()
                        ->integerNode('article_items_per_page')
                            ->defaultValue(10)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
