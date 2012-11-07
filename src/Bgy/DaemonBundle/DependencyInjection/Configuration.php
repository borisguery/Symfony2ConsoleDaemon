<?php

namespace Bgy\DaemonBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bgy_daemon');

        $rootNode
            ->children()
                ->scalarNode('pid_file')
                    ->info('The name of the PID file')
                    ->example('daemon.pid')
                    ->cannotBeEmpty()
                    ->defaultValue('bgy_daemon.pid')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
