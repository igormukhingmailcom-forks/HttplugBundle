<?php

namespace Http\HttplugBundle\ClientFactory;

use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;

/**
 * This factory creates a PluginClient.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
final class PluginClientFactory
{
    /**
     * @param Plugin[]      $plugins
     * @param ClientFactory $factory
     * @param array         $config
     * @param array         $pluginClientOptions
     *
     * @return PluginClient
     */
    public static function createPluginClient(array $plugins, ClientFactory $factory, array $config, array $pluginClientOptions = [])
    {
        return new PluginClient($factory->createClient($config), $plugins, $pluginClientOptions);
    }
}
