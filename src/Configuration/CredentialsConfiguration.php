<?php
declare(strict_types=1);

namespace Leviy\ReleaseTool\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class CredentialsConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('credentials');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('github')
                ->children()
                    ->scalarNode('token')
                        ->isRequired()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
