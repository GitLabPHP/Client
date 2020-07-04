<?php

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
                self::handleError($status, ResponseMediator::getContent($response));
            }

            return $response;
        });
    }

    /**
     * Handle an error response.
     *
     * @param int          $status
     * @param array|string $content
     *
     * @throws ErrorException
     * @throws RuntimeException
     *
     * @return void
     */
    private static function handleError(int $status, $content)
    {
        if (is_array($content) && isset($content['message'])) {
            if (400 === $status || 422 === $status) {
                $message = self::parseMessage($content['message']);

                throw new ValidationFailedException($message, $status);
            }
        }

        /** @var array<string,mixed> $content */
        $message = null;
        if (isset($content['error'])) {
            $message = $content['error'];
            if (is_array($content['error'])) {
                $message = implode("\n", $content['error']);
            }
        } elseif (isset($content['message'])) {
            $message = self::parseMessage($content['message']);
        } else {
            $message = $content;
        }

        if (429 === $status) {
            throw new ApiLimitExceededException($message, $status);
        }

        throw new RuntimeException($message, $status);
    }

    /**
     * @param mixed $message
     *
     * @return string
     */
    private static function parseMessage($message)
    {
        $string = $message;

        if (is_array($message)) {
            $format = '"%s" %s';
            $errors = [];

            foreach ($message as $field => $messages) {
                if (is_array($messages)) {
                    $messages = array_unique($messages);
                    foreach ($messages as $error) {
                        $errors[] = sprintf($format, $field, $error);
                    }
                } elseif (is_int($field)) {
                    $errors[] = $messages;
                } else {
                    $errors[] = sprintf($format, $field, $messages);
                }
            }

            $string = implode(', ', $errors);
        }

        return $string;
    }
}
