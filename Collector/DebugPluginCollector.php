<?php

namespace Http\HttplugBundle\Collector;

use Http\Client\Exception;
use Http\Message\Formatter;
use Http\Message\Formatter\SimpleFormatter;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class DebugPluginCollector extends DataCollector
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var PluginJournal
     */
    private $journal;

    /**
     * @param Formatter $formatter
     */
    public function __construct(Formatter $formatter, PluginJournal $journal)
    {
        $this->formatter = $formatter;
        $this->journal = $journal;
    }

    /**
     * @param RequestInterface $request
     */
    public function addRequest(RequestInterface $request, $clientName, $depth)
    {
        $this->data[$clientName]['request'][$depth][] = $this->formatter->formatRequest($request);
    }

    /**
     * @param ResponseInterface $response
     */
    public function addResponse(ResponseInterface $response, $clientName, $depth)
    {
        $this->data[$clientName]['response'][$depth][] = $this->formatter->formatResponse($response);
    }

    /**
     * @param Exception $exception
     */
    public function addFailure(Exception $exception, $clientName, $depth)
    {
        if ($exception instanceof Exception\HttpException) {
            $formattedResponse = $this->formatter->formatResponse($exception->getResponse());
        } elseif ($exception instanceof Exception\TransferException) {
            $formattedResponse = $exception->getMessage();
        } else {
            $formattedResponse = sprintf('Unexpected exception of type "%s"', get_class($exception));
        }

        $this->data[$clientName]['response'][$depth][] = $formattedResponse;
        $this->data[$clientName]['failure'][$depth][] = true;
    }


    /**
     * Returns the successful request-resonse pairs.
     *
     * @return array
     */
    public function getSucessfulRequests()
    {
        $count = 0;
        foreach ($this->data as $client) {
            if (isset($client['request'])) {
                $count += count($client['request'][0]);
            }
        }

        return $count;
    }

    /**
     * Returns the failed request-resonse pairs.
     *
     * @return array
     */
    public function getFailedRequests()
    {
        $count = 0;
        foreach ($this->data as $client) {
            if (isset($client['failure'])) {
                $count += count($client['failure'][0]);
            }
        }

        return $count;
    }

    /**
     * Returns the total number of request made.
     *
     * @return int
     */
    public function getTotalRequests()
    {
        return $this->getSucessfulRequests() + $this->getFailedRequests();
    }

    /**
     *
     * @return ClientDataCollector[]
     */
    public function getClients()
    {
        return ClientDataCollector::createFromCollectedData($this->data);
    }

    /**
     * @return PluginJournal
     */
    public function getJournal()
    {
        return $this->journal;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // We do not need to collect any data from the Symfony Request and Response
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'httplug';
    }

    public function serialize()
    {
        return serialize([$this->data, $this->journal]);
    }

    public function unserialize($data)
    {
        list($this->data, $this->journal) = unserialize($data);
    }
}
