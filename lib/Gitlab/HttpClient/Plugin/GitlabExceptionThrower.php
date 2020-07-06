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
 * @final
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Fabien Bourigault <bourigaultfabien@gmail.com>
 */
class GitlabExceptionThrower implements Plugin
{
    use Plugin\VersionBridgePlugin;

    /**
     * Handle the request and return the response coming from the next callable.
     *
     * @param RequestInterface $request
     * @param callable         $next
     * @param callable         $first
     *
     * @return Promise
     */
    public function doHandleRequest(RequestInterface $request, callable $next, callable $first)
    {
        return $next($request)->then(function (ResponseInterface $response) {
            $status = $response->getStatusCode();

            if ($status >= 400 && $status < 600) {
                self::handleError($status, self::getMessage($response) ?: $response->getReasonPhrase());
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
    private static function handleError($status, $message)
    {
        if (400 === $status || 422 === $status) {
            throw new ValidationFailedException($message, $status);
        }

        if (429 === $status) {
            throw new ApiLimitExceededException($message, $status);
        }

        throw new RuntimeException($message, $status);
    }

    /**
     * Get the error message from the response if present.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return string|null
     */
    private static function getMessage(ResponseInterface $response)
    {
        $content = ResponseMediator::getContent($response);

        if (!is_array($content)) {
            return null;
        }

        if (isset($content['message'])) {
            $message = $content['message'];

            if (is_string($message)) {
                return $message;
            }

            if (is_array($message)) {
                return self::parseMessage($content['message']);
            }
        }

        if (isset($content['error_description'])) {
            $error = $content['error_description'];

            if (is_string($error)) {
                return $error;
            }
        }

        if (isset($content['error'])) {
            $error = $content['error'];

            if (is_string($error)) {
                return $error;
            }
        }

        return null;
    }

    /**
     * @param array $message
     *
     * @return string
     */
    private static function parseMessage(array $message)
    {
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

        return implode(', ', $errors);
    }
}
