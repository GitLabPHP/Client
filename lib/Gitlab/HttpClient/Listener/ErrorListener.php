<?php namespace Gitlab\HttpClient\Listener;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Gitlab\Exception\ErrorException;
use Gitlab\Exception\RuntimeException;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Matt Humphrey <git@m4tt.co>
 */
class ErrorListener implements ListenerInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

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
        /** @var $response \Gitlab\HttpClient\Message\Response */
        if ($response->isClientError() || $response->isServerError()) {
            $content = $response->getContent();
            if (is_array($content) && isset($content['message'])) {
                if (400 == $response->getStatusCode()) {
                    $message = $this->parseMessage($content['message']);

                    throw new ErrorException($message, 400);
                }
            }

            $errorMessage = null;
            if (isset($content['error'])) {
                $errorMessage = implode("\n", $content['error']);
            } elseif (isset($content['message'])) {
                $errorMessage = $this->parseMessage($content['message']);
            } else {
                $errorMessage = $content;
            }

            throw new RuntimeException($errorMessage, $response->getStatusCode());
        }
    }

    /**
     * @param mixed $message
     * @return string
     */
    protected function parseMessage($message)
    {
        $string = $message;

        if (is_array($message)) {
            $format = '"%s" %s';
            $errors = array();

            foreach ($message as $field => $messages) {
                if (is_array($messages)) {
                    $messages = array_unique($messages);
                    foreach ($messages as $error) {
                        $errors[] = sprintf($format, $field, $error);
                    }
                } else {
                    $errors[] = sprintf($format, $field, $errors);
                }
            }

            $string = implode(', ', $errors);
        }

        return $string;
    }
}
