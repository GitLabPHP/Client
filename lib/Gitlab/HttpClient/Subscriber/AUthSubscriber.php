<?php

namespace Gitlab\HttpClient\Subscriber;

use Gitlab\HttpClient\HttpClientInterface;
use Guzzle\Common\Event;
use Guzzle\Http\Message\Request;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthSubscriber implements EventSubscriberInterface
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

    public static function getSubscribedEvents()
    {
        return array('request.before_send' => 'beforeRequest');
    }

    public function beforeRequest(Event $event)
    {
        /**
         * @var Request
         */
        $request = $event['request'];

        if (null === $this->method) {
            return;
        }

        switch ($this->method) {
            case HttpClientInterface::AUTH_HTTP_TOKEN:
                $request->addHeader('PRIVATE-TOKEN', $this->token);
                if (!is_null($this->sudo)) {
                    $request->addHeader('SUDO', $this->sudo);
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
} 
