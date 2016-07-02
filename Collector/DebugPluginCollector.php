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
     * @param Formatter $formatter
     */
    public function __construct(Formatter $formatter = null)
    {
        $this->formatter = $formatter ?: new SimpleFormatter();
    }

    /**
     * @param RequestInterface $request
     */
    public function addRequest(RequestInterface $request, $clientName, $depth)
    {
        $this->data[$clientName]['requests'][$depth][] = $this->formatter->formatRequest($request);
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

        $this->data[$clientName]['failure'][$depth][] = $formattedResponse;
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
        return 'httplug_debug';
    }
}
