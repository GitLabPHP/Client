<?php
namespace Gitlab\HttpClient\Adapter;

use Buzz\Client\AbstractClient;
use Buzz\Client\Curl;
use Buzz\Listener\ListenerInterface;
use Gitlab\Exception\ErrorException;
use Gitlab\Exception\RuntimeException;
use Gitlab\HttpClient\Listener\AuthListener;
use Gitlab\HttpClient\Listener\ErrorListener;
use Gitlab\HttpClient\Message\Request;
use Gitlab\HttpClient\Message\BuzzResponse;

class BuzzAdapter implements AdapterInterface
{
    /**
     * @var AbstractClient
     */
    protected $client;

    /**
     * @var ListenerInterface[]
     */
    protected $listeners = array();

    /**
     * @param AbstractClient $client
     */
    public function __construct(AbstractClient $client = null)
    {
        $this->client = $client ?: new Curl();

        $this->addListener(new ErrorListener());
    }

    /**
     * @param int $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->client->setTimeout($timeout);

        return $this;
    }

    /**
     * @param bool $verify
     *
     * @return $this
     */
    public function verifyPeer($verify)
    {
        $this->client->setVerifyPeer($verify);

        return $this;
    }

    /**
     * @param string $path
     * @param array  $parameters
     * @param string $httpMethod
     * @param array  $headers
     *
     * @throws ErrorException
     * @throws RuntimeException
     *
     * @return BuzzResponse
     */
    public function request($path, array $parameters = array(), $httpMethod = 'GET', array $headers = array())
    {
        $request = new Request($httpMethod);
        $request->setHeaders($headers);
        $request->fromUrl($path);
        $request->setContent(http_build_query($parameters));

        $hasListeners = 0 < count($this->listeners);
        if ($hasListeners) {
            foreach ($this->listeners as $listener) {
                $listener->preSend($request);
            }
        }

        $response = new BuzzResponse();

        try {
            $this->client->send($request, $response);
        } catch (\LogicException $e) {
            throw new ErrorException($e->getMessage(), $e->getCode(), $e);
        } catch (\RuntimeException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        if ($hasListeners) {
            foreach ($this->listeners as $listener) {
                $listener->postSend($request, $response);
            }
        }

        return $response;
    }

    public function authenticate($token, $authMethod, $sudo = null)
    {
        $this->addListener(
            new AuthListener(
                $authMethod,
                $token,
                $sudo
            )
        );
    }

    public function addListener(ListenerInterface $listener)
    {
        $this->listeners[get_class($listener)] = $listener;
    }
}
