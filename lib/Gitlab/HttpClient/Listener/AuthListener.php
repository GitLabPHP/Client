<?php

namespace Gitlab\HttpClient\Listener;

use Gitlab\Client;
use Gitlab\Exception\InvalidArgumentException;

use Buzz\Listener\ListenerInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;
use Buzz\Util\Url;

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
     * @param string    $method
     * @param string    $token
     */
    public function __construct($method, $token, $sudo=null)
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
        // Skip by default
        if (null === $this->method) {
            return;
        }

        switch ($this->method) {
            case Client::AUTH_HTTP_TOKEN:
                $request->addHeader('private_token: '.$this->token);
                if(!is_null($this->sudo)){
                    $request->addHeader('SUDO: '.$this->sudo);
                }
                break;

            case Client::AUTH_URL_TOKEN:
                $url  = $request->getUrl();
                $url .= (false === strpos($url, '?') ? '?' : '&').utf8_encode(http_build_query(array('private_token' => $this->token), '', '&'));
                if(!is_null($this->sudo)){
                    $url.="&".utf8_encode(http_build_query(array('sudo' => $this->sudo), '', '&'));
                }
                $request->fromUrl(new Url($url));
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
