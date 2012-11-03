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

        $this->loadBrowser($container, $config['browser']);

        $this->loadAccessTokenCache($container, $config['access_token_cache']);

        $container->setParameter('microsoft_oauth.client_id', $config['microsoft_oauth']['client_id']);
        $container->setParameter('microsoft_oauth.client_secret', $config['microsoft_oauth']['client_secret']);
    }

    private function loadBrowser(ContainerBuilder $container, array $config)
    {
        $client = $config['client'];

        $clientClassMap = array(
            'file_get_contents' => 'Buzz\\Client\\FileGetContents',
            'curl' => 'Buzz\\Client\\Curl',
        );

        if (!isset($clientClassMap[$client])) {
            throw new \InvalidArgumentException(sprintf('Client "%s" is not supported', $client));
        }

        $clientClass = $clientClassMap[$client];
        $clientDefinition = new Definition($clientClass);
        $clientDefinition->setPublic(false);

        $container->setDefinition('matthiasnoback_microsoft_translator.browser_client', $clientDefinition);
    }

    private function loadAccessTokenCache(ContainerBuilder $container, array $config)
    {
        $cache = $config['cache'];

        if (false === $cache) {
            return;
        }

        $cacheClassMap = array(
            'array' => 'Doctrine\\Common\\Cache\\ArrayCache',
            'apc'   => 'Doctrine\\Common\\Cache\\ApcCache',
        );

        if (!isset($cacheClassMap[$cache])) {
            throw new \InvalidArgumentException(sprintf('Cache "%s" is not supported', $cache));
        }

        $cacheClass = $cacheClassMap[$cache];
        $cacheDefinition = new Definition($cacheClass);
        $cacheDefinition->setPublic(false);

        $cacheServiceId = 'matthiasnoback_microsoft_translator.access_token_cache.cache';
        $container->setDefinition($cacheServiceId, $cacheDefinition);
    }
}
