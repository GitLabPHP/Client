<?php

namespace Gitlab\HttpClient;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\Cache\Generator\HeaderCacheKeyGenerator;
use Http\Client\Common\Plugin\CachePlugin;
use Http\Client\Common\PluginClientFactory;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;
use Http\Message\UriFactory;
use Psr\Cache\CacheItemPoolInterface;

/**
 * The HTTP client builder class.
 *
 * This will allow you to fluently add and remove plugins.
 *
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
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
     * The HTTP request factory.
     *
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * The HTTP stream factory.
     *
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * The URI factory.
     *
     * @var UriFactory
     */
    private $uriFactory;

    /**
     * The currently registered plugins.
     *
     * @var Plugin[]
     */
    private $plugins = [];

    /**
     * The cache plugin to use.
     *
     * This plugin is specially treated because it has to be the very last plugin.
     *
     * @var CachePlugin|null
     */
    private $cachePlugin;

    /**
     * A HTTP client with all our plugins.
     *
     * @var HttpMethodsClient|null
     */
    private $pluginClient;

    /**
     * Create a new http client builder instance.
     *
     * @param HttpClient|null     $httpClient
     * @param RequestFactory|null $requestFactory
     * @param StreamFactory|null  $streamFactory
     * @param UriFactory|null     $uriFactory
     *
     * @return void
     */
    public function __construct(
        HttpClient $httpClient = null,
        RequestFactory $requestFactory = null,
        StreamFactory $streamFactory = null,
        UriFactory $uriFactory = null
    ) {
        $this->httpClient = null === $httpClient ? HttpClientDiscovery::find() : $httpClient;
        $this->requestFactory = null === $requestFactory ? MessageFactoryDiscovery::find() : $requestFactory;
        $this->streamFactory = null === $streamFactory ? StreamFactoryDiscovery::find() : $streamFactory;
        $this->uriFactory = null === $uriFactory ? UriFactoryDiscovery::find() : $uriFactory;
    }

    /**
     * @return HttpMethodsClient
     */
    public function getHttpClient()
    {
        if (null === $this->pluginClient) {
            $plugins = $this->plugins;
            if (null !== $this->cachePlugin) {
                $plugins[] = $this->cachePlugin;
            }

            $this->pluginClient = new HttpMethodsClient(
                (new PluginClientFactory())->createClient($this->httpClient, $plugins),
                $this->requestFactory
            );
        }

        return $this->pluginClient;
    }

    /**
     * Get the request factory.
     *
     * @return RequestFactory
     */
    public function getRequestFactory()
    {
        return $this->requestFactory;
    }

    /**
     * Get the stream factory.
     *
     * @return StreamFactory
     */
    public function getStreamFactory()
    {
        return $this->streamFactory;
    }

    /**
     * Get the URI factory.
     *
     * @return UriFactory
     */
    public function getUriFactory()
    {
        return $this->uriFactory;
    }

    /**
     * Add a new plugin to the end of the plugin chain.
     *
     * @param Plugin $plugin
     *
     * @return void
     */
    public function addPlugin(Plugin $plugin)
    {
        $this->plugins[] = $plugin;
        $this->pluginClient = null;
    }

    /**
     * Remove a plugin by its fully qualified class name (FQCN).
     *
     * @param string $fqcn
     *
     * @return void
     */
    public function removePlugin($fqcn)
    {
        foreach ($this->plugins as $idx => $plugin) {
            if ($plugin instanceof $fqcn) {
                unset($this->plugins[$idx]);
                $this->pluginClient = null;
            }
        }
    }

    /**
     * Add a cache plugin to cache responses locally.
     *
     * @param CacheItemPoolInterface $cachePool
     * @param array                  $config
     *
     * @return void
     */
    public function addCache(CacheItemPoolInterface $cachePool, array $config = [])
    {
        if (!isset($config['cache_key_generator'])) {
            $config['cache_key_generator'] = new HeaderCacheKeyGenerator(['Authorization', 'Cookie', 'Accept', 'Content-type']);
        }

        $this->cachePlugin = CachePlugin::clientCache($cachePool, $this->streamFactory, $config);
        $this->pluginClient = null;
    }

    /**
     * Remove the cache plugin.
     *
     * @return void
     */
    public function removeCache()
    {
        $this->cachePlugin = null;
        $this->pluginClient = null;
    }
}
