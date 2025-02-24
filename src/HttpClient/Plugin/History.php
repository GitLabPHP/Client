<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    private ?ResponseInterface $lastResponse;

    public function __construct()
    {
        $this->lastResponse = null;
    }

    /**
     * Get the last response.
     */
    public function getLastResponse(): ?ResponseInterface
    {
        return $this->lastResponse;
    }

    /**
     * Record a successful call.
     */
    public function addSuccess(RequestInterface $request, ResponseInterface $response): void
    {
        $this->lastResponse = $response;
    }

    /**
     * Record a failed call.
     */
    public function addFailure(RequestInterface $request, ClientExceptionInterface $exception): void
    {
    }
}
