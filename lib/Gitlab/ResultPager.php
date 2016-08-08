<?php namespace Gitlab;

use Gitlab\Api\ApiInterface;

/**
 * Pager class for supporting pagination in Gitlab classes
 */
class ResultPager implements ResultPagerInterface
{
    /**
     * @var \Gitlab\Client client
     */
    protected $client;

    /**
     * The Gitlab client to use for pagination. This must be the same
     * instance that you got the Api instance from, i.e.:
     *
     * $client = new \Gitlab\Client();
     * $api = $client->api('someApi');
     * $pager = new \Gitlab\ResultPager($client);
     *
     * @param \Gitlab\Client $client
     *
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(ApiInterface $api, $method, array $parameters = array())
    {
        $result = call_user_func_array(array($api, $method), $parameters);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll(ApiInterface $api, $method, array $parameters = array())
    {
        $result = call_user_func_array(array($api, $method), $parameters);

        while ($this->hasNext()) {
            $result = array_merge($result, $this->fetchNext());
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function hasNext()
    {
        return $this->has('next');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchNext()
    {
        return $this->get('next');
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrevious()
    {
        return $this->has('prev');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchPrevious()
    {
        return $this->get('prev');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchFirst()
    {
        return $this->get('first');
    }

    /**
     * {@inheritdoc}
     */
    public function fetchLast()
    {
        return $this->get('last');
    }

    /**
     * {@inheritdoc}
     */
    protected function has($key)
    {
        return !empty($this->client->getHttpClient()->getLastResponse()->getPagination()) && isset($this->client->getHttpClient()->getLastResponse()->getPagination()[$key]);
    }

    /**
     * {@inheritdoc}
     */
    protected function get($key)
    {
        if ($this->has($key)) {
            $result = $this->client->getHttpClient()->get(strtr($this->client->getHttpClient()->getLastResponse()->getPagination()[$key], array($this->client->getBaseUrl() => '')))->getContent();
            return $result;
        }
    }
}
