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

        $rootNode
            ->children()
                ->arrayNode('oauth')
                    ->isRequired()
                    ->children()
                        ->scalarNode('client_id')
                            ->info('Client ID of your application for Microsoft OAuth')
                            ->isRequired()
                        ->end()
                        ->scalarNode('client_secret')
                            ->info('Client secret of your application for Microsoft OAuth')
                            ->isRequired()
                        ->end()
                        ->scalarNode('browser_client')
                            ->info('Service id of a Buzz browser client implementation')
                            ->defaultNull()
                        ->end()
                        ->scalarNode('access_token_cache')
                            ->info('Service id of a cache implementation for access token caching')
                            ->defaultNull()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('translator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('browser_client')
                            ->defaultNull()
                            ->info('Service id of a Buzz browser client implementation')
                        ->end()
                        ->arrayNode('response_cache')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('id')
                                    ->info('Service id of a cache implementation for response caching')
                                    ->defaultNull()
                                ->end()
                                ->scalarNode('lifetime')
                                    ->info('The lifetime of the cached responses in seconds')
                                    ->defaultValue(0)
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('response_cache')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
