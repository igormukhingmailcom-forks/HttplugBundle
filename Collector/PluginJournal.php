<?php

namespace Http\HttplugBundle\Collector;

/**
 * A object that remembers what plugins are configured for which clients.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class PluginJournal
{
    /**
     * @var array ['clientName'=>['index' => 'PluginName']
     */
    private $data;

    /**
     * @return array
     */
    public function getPlugins($clientName)
    {
        return $this->data[$clientName];
    }

    /**
     * @return string|null
     */
    public function getPluginName($clientName, $idx)
    {
        if (isset($this->data[$clientName][$idx])) {
            return $this->data[$clientName][$idx];
        }

        return;
    }

    /**
     * @param string $clientName
     * @param array $plugins
     *
     * @return $this
     */
    public function setPlugins($clientName, array $plugins)
    {
        $this->data[$clientName] = $plugins;

        return $this;
    }

}