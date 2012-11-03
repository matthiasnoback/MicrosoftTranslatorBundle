<?php

namespace MatthiasNoback\MicrosoftTranslatorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('matthiasnoback_microsoft_translator');

        $validClients = array('file_get_contents', 'curl');
        $validCacheTypes = array('array', 'apc');

        $rootNode
            ->children()
                ->arrayNode('microsoft_oauth')
                    ->isRequired()
                    ->children()
                        ->scalarNode('client_id')
                            ->isRequired()
                        ->end()
                        ->scalarNode('client_secret')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('browser')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('client')
                            ->validate()
                                ->ifNotInArray($validClients)
                                ->then(function() use ($validClients) {
                                    throw new \InvalidArgumentException(sprintf(
                                        'Invalid client, should be one of: %s',
                                        implode(', ', $validClients)
                                    ));
                                })
                            ->end()
                            ->defaultValue($validClients[0])
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('access_token_cache')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('cache')
                            ->validate()
                                ->ifNotInArray($validCacheTypes)
                                ->then(function() use ($validCacheTypes) {
                                    throw new \InvalidArgumentException(sprintf(
                                        'Invalid cache type, should be one of: %s',
                                        implode(', ', $validCacheTypes)
                                    ));
                                })
                            ->end()
                            ->defaultValue($validCacheTypes[0])
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
