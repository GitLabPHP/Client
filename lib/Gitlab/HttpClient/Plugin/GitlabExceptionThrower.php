<?php

declare(strict_types=1);

namespace Gitlab\HttpClient\Plugin;

use Gitlab\Exception\ApiLimitExceededException;
use Gitlab\Exception\ErrorException;
use Gitlab\Exception\RuntimeException;
use Gitlab\Exception\ValidationFailedException;
use Gitlab\HttpClient\Message\ResponseMediator;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A plugin to remember the last response.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Fabien Bourigault <bourigaultfabien@gmail.com>
 *
 * @internal
 */
final class GitlabExceptionThrower implements Plugin
{
    /**
     * Handle the request and return the response coming from the next callable.
     *
     * @param RequestInterface $request
     * @param callable         $next
     * @param callable         $first
     *
     * @return Promise
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        return $next($request)->then(function (ResponseInterface $response) {
            $status = $response->getStatusCode();

            if ($status >= 400 && $status < 600) {
                self::handleError($status, ResponseMediator::getErrorMessage($response) ?: $response->getReasonPhrase());
            }

            return $response;
        });
    }

    /**
     * Handle an error response.
     *
     * @param int    $status
     * @param string $message
     *
     * @throws ErrorException
     * @throws RuntimeException
     *
     * @return void
     */
    private static function handleError(int $status, string $message)
    {
        if (400 === $status || 422 === $status) {
            throw new ValidationFailedException($message, $status);
        }

        if (429 === $status) {
            throw new ApiLimitExceededException($message, $status);
        }

        throw new RuntimeException($message, $status);
    }
}
