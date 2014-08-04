<?php

namespace Gitlab\HttpClient\Listener;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Gitlab\Exception\ErrorException;
use Gitlab\Exception\RuntimeException;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ErrorListener implements ListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function preSend(RequestInterface $request)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function postSend(RequestInterface $request, MessageInterface $response)
    {
        /** @var $response \Gitlab\HttpClient\Message\BuzzResponse */
        if ($response->isClientError() || $response->isServerError()) {
            $content = $response->getContent();
            if (is_array($content) && isset($content['message'])) {
                if (400 == $response->getStatusCode()) {
                    throw new ErrorException($content['message'], 400);
                }
            }

            $errorMessage = null;
            if (isset($content['error'])) {
                $errorMessage = implode("\n", $content['error']);
            } elseif (isset($content['message'])) {
                $errorMessage = $content['message'];
            } else {
                $errorMessage = $content;
            }

            throw new RuntimeException($errorMessage, $response->getStatusCode());
        }
    }
}
