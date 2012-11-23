<?php

namespace Gitlab;

use Buzz\Client\Curl;
use Buzz\Client\ClientInterface;

use Gitlab\Api\ApiInterface;
use Gitlab\Exception\InvalidArgumentException;
use Gitlab\HttpClient\HttpClient;
use Gitlab\HttpClient\HttpClientInterface;
use Gitlab\HttpClient\Listener\AuthListener;

/**
 * Simple yet very cool PHP GitHub client
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 *
 * Website: http://github.com/KnpLabs/php-github-api
 */
class Client
{
    /**
     * Constant for authentication method. Indicates the default, but deprecated
     * login with username and token in URL.
     */
    const AUTH_URL_TOKEN = 'url_token';

    /**
     * Constant for authentication method. Indicates the new login method with
     * with username and token via HTTP Authentication.
     */
    const AUTH_HTTP_TOKEN = 'http_token';

    /**
     * @var array
     */
    private $options = array(
        'user_agent'  => 'php-gitlab-api (http://github.com/m4tthumphrey/php-gitlab-api)',
        'timeout'     => 10
    );

    private $base_url = null;

    /**
     * The Buzz instance used to communicate with GitHub
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Instantiate a new GitHub client
     *
     * @param null|ClientInterface $httpClient Buzz client
     */
    public function __construct($baseUrl, ClientInterface $httpClient = null)
    {
        $httpClient = $httpClient ?: new Curl();
        $httpClient->setTimeout($this->options['timeout']);
        $httpClient->setVerifyPeer(false);

        $this->base_url     = $baseUrl;
        $this->httpClient   = new HttpClient($this->base_url, $this->options, $httpClient);
    }

    /**
     * @param string $name
     *
     * @return ApiInterface
     *
     * @throws InvalidArgumentException
     */
    public function api($name)
    {
        switch ($name) {
            case 'users':
                $api = new Api\Users($this);
                break;

            case 'projects':
                $api = new Api\Projects($this);
                break;

            default:
                throw new InvalidArgumentException();
        }

        return $api;
    }

    /**
     * Authenticate a user for all next requests
     *
     * @param string      $tokenOrLogin  GitHub private token/username/client ID
     * @param null|string $password      GitHub password/secret
     * @param null|string $authMethod    One of the AUTH_* class constants
     */
    public function authenticate($token, $authMethod = null)
    {
        $this->httpClient->addListener(
            new AuthListener(
                $authMethod,
                $token
            )
        );
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function setBaseUrl($url)
    {
        $this->base_url = $url;
    }
    public function getBaseUrl()
    {
        return $this->base_url;
    }

    /**
     * Clears used headers
     */
    public function clearHeaders()
    {
        $this->httpClient->clearHeaders();
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->httpClient->setHeaders($headers);
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(sprintf('Undefined option called: "%s"', $name));
        }

        return $this->options[$name];
    }


    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(sprintf('Undefined option called: "%s"', $name));
        }

        $this->options[$name] = $value;
    }
}
