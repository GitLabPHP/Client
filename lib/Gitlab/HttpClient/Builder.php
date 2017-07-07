<?php

namespace Gitlab\HttpClient;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Message\MessageFactory;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;

/**
 * A builder that builds the API client.
 * This will allow you to fluently add and remove plugins.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Builder
{
    /**
     * The object that sends HTTP messages.
     *
     * @var HttpClient
     */
    private $httpClient;

    /**
     * A HTTP client with all our plugins.
     *
     * @var PluginClient
     */
    private $pluginClient;

    /**
     * @var MessageFactory
     */
    private $requestFactory;

    /**
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * True if we should create a new Plugin client at next request.
     *
     * @var bool
     */
    private $httpClientModified = true;

    /**
     * @var Plugin[]
     */
    private $plugins = [];

    /**
     * @param HttpClient     $httpClient
     * @param RequestFactory $requestFactory
     * @param StreamFactory  $streamFactory
     */
    public function __construct(
        HttpClient $httpClient = null,
        RequestFactory $requestFactory = null,
        StreamFactory $streamFactory = null
    ) {
        $this->httpClient = $httpClient ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
        $this->streamFactory = $streamFactory ?: StreamFactoryDiscovery::find();
    }

    /**
     * @return HttpMethodsClient
     */
    public function getHttpClient()
    {
        if ($this->httpClientModified) {
            $this->httpClientModified = false;

            $this->pluginClient = new HttpMethodsClient(
                new PluginClient($this->httpClient, $this->plugins),
                $this->requestFactory
            );
        }

        return $this->pluginClient;
    }

    /**
     * Add a new plugin to the end of the plugin chain.
     *
     * @param Plugin $plugin
     */
    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[] = $plugin;
        $this->httpClientModified = true;
    }

    /**
     * Remove a plugin by its fully qualified class name (FQCN).
     *
     * @param string $fqcn
     */
    public function removePlugin($fqcn)
    {
        foreach ($this->plugins as $idx => $plugin) {
            if ($plugin instanceof $fqcn) {
                unset($this->plugins[$idx]);
                $this->httpClientModified = true;
            }
        }
    }
}
