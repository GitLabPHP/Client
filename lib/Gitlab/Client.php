<?php

namespace Gitlab;

use Gitlab\Api\ApiInterface;
use Gitlab\Exception\InvalidArgumentException;
use Gitlab\HttpClient\Adapter\AdapterInterface;
use Gitlab\HttpClient\HttpClient;

/**
 * Simple API wrapper for Gitlab
 */
class Client
{
    /**
     * @var array
     */
    private $options = array(
        'user_agent'  => 'php-gitlab-api (http://github.com/m4tthumphrey/php-gitlab-api)',
        'timeout'     => 60
    );

    /**
     * The HTTP client instance used to communicate with Gitlab
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * Instantiate a new Gitlab client
     *
     * @param string           $baseUrl
     * @param AdapterInterface $adapter
     */
    public function __construct($baseUrl, AdapterInterface $adapter)
    {
        $this->httpClient = new HttpClient($baseUrl, $adapter);
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

            case 'namespaces':
                $api = new Api\ProjectNamespaces($this);
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

            case 'snippets':
                $api = new Api\Snippets($this);
                break;

            default:
                throw new InvalidArgumentException();
        }

        return $api;
    }

    /**
     * Authenticate a user for all next requests
     *
     * @param string      $token      Gitlab private token
     * @param null|string $authMethod One of the AUTH_* class constants
     * @param null|string $sudo
     */
    public function authenticate($token, $authMethod = null, $sudo = null)
    {
        $this->httpClient->authenticate($token, $authMethod, $sudo);
    }

    public function get($path, array $parameters = array(), array $headers = array())
    {
        return $this->httpClient->get($path, $parameters, $headers);
    }

    public function post($path, array $parameters = array(), array $headers = array())
    {
        return $this->httpClient->post($path, $parameters, $headers);
    }

    public function patch($path, array $parameters = array(), array $headers = array())
    {
        return $this->httpClient->patch($path, $parameters, $headers);
    }

    public function delete($path, array $parameters = array(), array $headers = array())
    {
        return $this->httpClient->delete($path, $parameters, $headers);
    }

    public function put($path, array $parameters = array(), array $headers = array())
    {
        return $this->httpClient->put($path, $parameters, $headers);
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
     */
    public function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException(sprintf('Undefined option called: "%s"', $name));
        }

        $this->options[$name] = $value;
    }
}
