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
 * Simple API wrapper for Gitlab
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
        'timeout'     => 60
    );

    private $base_url = null;

    /**
     * The Buzz instance used to communicate with Gitlab
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Instantiate a new Gitlab client
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

            case 'repo':
            case 'repositories':
                $api = new Api\Repositories($this);
                break;

            case 'mr':
            case 'merge_requests':
                $api = new Api\MergeRequests($this);
                break;

            case 'issues':
                $api = new Api\Issues($this);
                break;

            case 'milestones':
            case 'ms':
                $api = new Api\Milestones($this);
                break;

            case 'groups':
                $api = new Api\Groups($this);
                break;

            case 'hooks':
            case 'system_hooks':
                $api = new Api\SystemHooks($this);
                break;

            default:
                throw new InvalidArgumentException();
        }

        return $api;
    }

    /**
     * Authenticate a user for all next requests
     *
     * @param string      $token  Gitlab private token
     * @param null|string $authMethod    One of the AUTH_* class constants
     */
    public function authenticate($token, $authMethod = null, $sudo=null)
    {
        $this->httpClient->addListener(
            new AuthListener(
                $authMethod,
                $token,
                $sudo
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
