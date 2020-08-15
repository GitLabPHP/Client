<?php

declare(strict_types=1);

namespace Gitlab\HttpClient\Plugin;

use Http\Client\Common\Plugin\Journal;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A plugin to remember the last response.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * @internal
 */
final class History implements Journal
{
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
    public function addSuccess(RequestInterface $request, ResponseInterface $response): void
    {
        $this->lastResponse = $response;
    }

    /**
     * Record a failed call.
     *
     * @param RequestInterface         $request
     * @param ClientExceptionInterface $exception
     *
     * @return void
     */
    public function addFailure(RequestInterface $request, ClientExceptionInterface $exception): void
    {
    }
}
