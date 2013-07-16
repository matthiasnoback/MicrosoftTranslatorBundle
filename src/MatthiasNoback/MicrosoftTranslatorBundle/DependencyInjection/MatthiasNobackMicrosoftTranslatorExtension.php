<?php

namespace MatthiasNoback\MicrosoftTranslatorBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class MatthiasNobackMicrosoftTranslatorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->loadOAuth($container, $config['oauth']);

        $this->loadTranslator($container, $config['translator']);
    }

    private function loadOAuth(ContainerBuilder $container, array $config)
    {
        $container->setParameter('microsoft_oauth.client_id', $config['client_id']);
        $container->setParameter('microsoft_oauth.client_secret', $config['client_secret']);

        $this->loadAccessTokenProviderBrowser($container, $config);

        $this->loadAccessTokenCache($container, $config);
    }

    private function loadTranslator(ContainerBuilder $container, array $config)
    {
        $this->loadTranslatorBrowserClient($container, $config);

        $this->loadTranslatorBrowserClientCache($container, isset($config['response_cache']) ? $config['response_cache'] : array());
    }

    /**
     * Configures a client for the Buzz browser of the access token provider
     * Use the one from the configuration, or use a default implementation
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     */
    private function loadAccessTokenProviderBrowser(ContainerBuilder $container, array $config)
    {
        if ($config['browser_client']) {
            $clientServiceId = $config['browser_client'];
        }
        else {

            $clientServiceId = 'matthiasnoback_microsoft_translator.access_token_provider.browser_client';
            $this->createDefaultBrowserClientDefinition($clientServiceId, $container);
        }

        $browserDefinition = $container->getDefinition('matthiasnoback_microsoft_translator.acces_token_provider.browser');
        $browserDefinition->replaceArgument(0, new Reference($clientServiceId));
    }

    /**
     * Configure a cache implementation for the access token cache
     * Use the one from the configuration, or use a default implementation
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     */
    private function loadAccessTokenCache(ContainerBuilder $container, array $config)
    {
        if ($config['access_token_cache']) {
            // the value of "access_token_cache" is the id of the service to be used for the cache implementation
            $cacheServiceId = $config['access_token_cache'];
        }
        else {
            // set up a default cache implementation
            $cacheServiceId = 'matthiasnoback_microsoft_translator.access_token_cache.cache';
            $this->createDefaultCacheServiceDefinition($cacheServiceId, $container);
        }

        $accessTokenCacheDefinition = $container->getDefinition('matthiasnoback_microsoft_translator.access_token_cache');
        $accessTokenCacheDefinition->replaceArgument(0, new Reference($cacheServiceId));
    }

    /**
     * Configures a client for the Buzz browser of the translator
     * Use the one from the configuration, or use a default implementation
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     */
    private function loadTranslatorBrowserClient(ContainerBuilder $container, array $config)
    {
        $cachedClientDefinition = $container->getDefinition('matthiasnoback_microsoft_translator.cached_browser_client');

        if ($config['browser_client']) {
            $clientServiceId = $config['browser_client'];
        }
        else {
            $clientServiceId = 'matthiasnoback_microsoft_translator.browser_client';
            $this->createDefaultBrowserClientDefinition($clientServiceId, $container);
        }

        $cachedClientDefinition->replaceArgument(0, new Reference($clientServiceId));
    }

    /**
     * Create the default browser client as a service
     *
     * @param $clientServiceId
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    private function createDefaultBrowserClientDefinition($clientServiceId, ContainerBuilder $container)
    {
        $clientDefinition = new Definition('Buzz\Client\Curl');
        $container->setDefinition($clientServiceId, $clientDefinition);
    }

    /**
     * Configures the cache implementation for the cached browser client
     * Use the one from the configuration, or use a default implementation
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $config
     */
    private function loadTranslatorBrowserClientCache(ContainerBuilder $container, array $config)
    {
        $cachedClientDefinition = $container->getDefinition('matthiasnoback_microsoft_translator.cached_browser_client');

        if (isset($config['id']) && $config['id']) {
            $cacheServiceId = $config['id'];
        }
        else {
            $cacheServiceId = 'matthiasnoback_microsoft_translator.cached_browser_client.cache';
            $this->createDefaultCacheServiceDefinition($cacheServiceId, $container);
        }

        $lifetime = isset($config['lifetime']) ? (integer) $config['lifetime'] : 0;

        $cachedClientDefinition->replaceArgument(1, new Reference($cacheServiceId));
        $cachedClientDefinition->replaceArgument(2, $lifetime);
    }

    /**
     * Create a default cache implementation (Doctrine Common's ArrayCache)
     *
     * @param $cacheServiceId
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private function createDefaultCacheServiceDefinition($cacheServiceId, ContainerBuilder $container)
    {
        $cacheDefinition = new Definition('Doctrine\Common\Cache\ArrayCache');
        $container->setDefinition($cacheServiceId, $cacheDefinition);

        return $cacheDefinition;
    }
}
