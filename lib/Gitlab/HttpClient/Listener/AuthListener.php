<?php

namespace Gitlab\HttpClient\Listener;

use Buzz\Message\RequestInterface;
use Gitlab\Exception\InvalidArgumentException;
use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Gitlab\HttpClient\HttpClientInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class AuthListener implements ListenerInterface
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string|null
     */
    private $sudo;

    /**
     * @param string      $method
     * @param string      $token
     * @param string|null $sudo
     */
    public function __construct($method, $token, $sudo = null)
    {
        $this->method  = $method;
        $this->token = $token;
        $this->sudo = $sudo;
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException
     */
    public function preSend(RequestInterface $request)
    {
        switch ($this->method) {
            case HttpClientInterface::AUTH_HTTP_TOKEN:
                $request->addHeader('PRIVATE-TOKEN:' . $this->token);
                if (!is_null($this->sudo)) {
                    $request->addHeader('SUDO:' . $this->sudo);
                }
                break;

            case HttpClientInterface::AUTH_URL_TOKEN:
                $url = $request->getUrl();
                $query = array('private_token' => $this->token);

                if (!is_null($this->sudo)) {
                    $query['sudo'] = $this->sudo;
                }

                $url .= (false === strpos($url, '?') ? '?' : '&').utf8_encode(http_build_query($query, '', '&'));

                $request->setUrl($url);
                break;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function postSend(RequestInterface $request, MessageInterface $response)
    {
    }
}
