<?php

namespace Gitlab\HttpClient\Plugin;

use Http\Client\Common\Plugin\Journal;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A plugin to remember the last response.
 *
 * @internal
 *
 * @final
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class History implements Journal
{
    use HistoryTrait;

    /**
     * @var ResponseInterface|null
     */
    private $lastResponse;

    /**
     * @return ResponseInterface|null
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Record a successful call.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return void
     */
    public function addSuccess(RequestInterface $request, ResponseInterface $response)
    {
        $this->lastResponse = $response;
    }
}
