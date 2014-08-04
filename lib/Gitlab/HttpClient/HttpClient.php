<?php

namespace Gitlab\HttpClient;

use Gitlab\HttpClient\Adapter\AdapterInterface;

/**
 * Performs requests on Gitlab API. API documentation should be self-explanatory.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class HttpClient implements HttpClientInterface
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @param string           $baseUrl
     * @param AdapterInterface $adapter
     */
    public function __construct($baseUrl, AdapterInterface $adapter)
    {
        $this->baseUrl = $baseUrl;
        $this->adapter = $adapter;

        $this->clearHeaders();
    }

    /**
     * {@inheritDoc}
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * Clears used headers
     */
    public function clearHeaders()
    {
        $this->headers = array();
    }

    /**
     * {@inheritDoc}
     */
    public function get($path, array $parameters = array(), array $headers = array())
    {
        if (0 < count($parameters)) {
            $path .= (false === strpos($path, '?') ? '?' : '&') . http_build_query($parameters, '', '&');
        }

        return $this->request($path, array(), 'GET', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function post($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'POST', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function patch($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'PATCH', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'DELETE', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function put($path, array $parameters = array(), array $headers = array())
    {
        return $this->request($path, $parameters, 'PUT', $headers);
    }

    /**
     * {@inheritDoc}
     */
    public function request($path, array $parameters = array(), $httpMethod = 'GET', array $headers = array())
    {
        $path = '/' . trim($path, '/') . '/';
        $headers = array_merge($this->headers, $headers);

        return $this->adapter->request($this->baseUrl . $path, $parameters, $httpMethod, $headers);
    }

    public function authenticate($token, $authMethod = null, $sudo = null)
    {
        $this->adapter->authenticate($token, $authMethod, $sudo);
    }
}
