<?php

namespace Http\HttplugBundle\Collector;

/**
 * An object to handle the collected data for a client.
 *
 * The Request object at $requests[0][2] is the state of the object between the third
 * and the fourth plugin. The response after that plugin is found in $responses[0][2].
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class ClientDataCollector
{
    /**
     * A multidimensional array with requests.
     * $requests[0][0] is the first request before all plugins.
     * $requests[0][1] is the first request after the first plugin.
     *
     * @var array
     */
    private $requests;

    /**
     * A multidimensional array with responses.
     * $responses[0][0] is the first responses before all plugins.
     * $responses[0][1] is the first responses after the first plugin.
     *
     * @var array
     */
    private $responses;

    /**
     *
     * @param array $requests
     * @param array $responses
     */
    public function __construct(array $requests, array $responses)
    {
        $this->requests = $requests;
        $this->responses = $responses;
    }

    /**
     * Create an array of ClientDataCollector from collected data.
     *
     * @param array $data
     *
     * @return ClientDataCollector[]
     */
    public static function createFromCollectedData(array $data)
    {
        $clientData = [];
        foreach ($data as $clientName => $messages) {
            $clientData[$clientName] = static::createOne($messages);
        }

        return $clientData;
    }

    /**
     * @param array $messages
     *
     * @return ClientDataCollector
     */
    private static function createOne($messages) 
    {
        $orderedRequests = [];
        $orderedResponses = [];

        foreach ($messages['request'] as $depth => $requests) {
            foreach ($requests as $idx => $request) {
                $orderedRequests[$idx][$depth] = $request;
            }
         }

        foreach ($messages['response'] as $depth => $responses) {
            foreach ($responses as $idx => $response) {
                $orderedResponses[$idx][$depth] = $response;
            }
         }

        return new self($orderedRequests, $orderedResponses);
    }

    /**
     * @return array
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @param array $requests
     *
     * @return ClientDataCollector
     */
    public function setRequests($requests)
    {
        $this->requests = $requests;

        return $this;
    }

    /**
     * @return array
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @param array $responses
     *
     * @return ClientDataCollector
     */
    public function setResponses($responses)
    {
        $this->responses = $responses;

        return $this;
    }

    /**
     * Get the index keys for the request and response stacks.
     *
     * @return array
     */
    public function getStackIndexKeys()
    {
        return array_keys($this->requests);
    }

    /**
     * @param int $idx
     *
     * @return array responses
     */
    public function getRequstStack($idx)
    {
        return $this->requests[$idx];
    }

    /**
     * @param int $idx
     *
     * @return array responses
     */
    public function getResponseStack($idx)
    {
        return $this->responses[$idx];
    }
}