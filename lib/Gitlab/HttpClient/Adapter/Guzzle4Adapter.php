<?php
namespace Gitlab\HttpClient\Adapter;

use Gitlab\Exception\ErrorException;
use Gitlab\Exception\RuntimeException;
use Gitlab\HttpClient\Message\Guzzle4Response;
use Gitlab\HttpClient\Subscriber\AuthSubscriber;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class Guzzle4Adapter implements AdapterInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?: new Client();
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
     * @return Guzzle4Response
     */
    public function request($path, array $parameters = array(), $httpMethod = 'GET', array $headers = array())
    {
        $request = $this->client->createRequest($httpMethod, $path, $headers, http_build_query($parameters));

        try {
            $response = $this->client->send($request);
        } catch (\LogicException $e) {
            throw new ErrorException($e->getMessage(), $e->getCode(), $e);
        } catch (\RuntimeException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return new Guzzle4Response($response);
    }

    public function authenticate($token, $authMethod, $sudo = null)
    {
        $this->client->getEmitter()->attach(new AuthSubscriber($authMethod, $token, $sudo));
    }
}
