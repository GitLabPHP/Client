<?php

namespace Gitlab;

use Gitlab\Api\ApiInterface;
use Gitlab\HttpClient\Message\ResponseMediator;

/**
 * This is the result pager class.
 *
 * @final
 *
 * @author Ramon de la Fuente <ramon@future500.nl>
 * @author Mitchel Verschoof <mitchel@future500.nl>
 * @author Graham Campbell <graham@alt-three.com>
 */
class ResultPager implements ResultPagerInterface
{
    /**
     * The client to use for pagination.
     *
     * @var \Gitlab\Client client
     */
    protected $client;

    /**
     * Create a new result pager instance.
     *
     * @param \Gitlab\Client $client
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(ApiInterface $api, $method, array $parameters = [])
    {
        return call_user_func_array([$api, $method], $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll(ApiInterface $api, $method, array $parameters = [])
    {
        $result = call_user_func_array([$api, $method], $parameters);
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
     * @param string $key
     *
     * @return bool
     */
    protected function has($key)
    {
        $lastResponse = $this->client->getLastResponse();
        if (null == $lastResponse) {
            return false;
        }

        $pagination = ResponseMediator::getPagination($lastResponse);
        if (null == $pagination) {
            return false;
        }

        return isset($pagination[$key]);
    }

    /**
     * @param string $key
     *
     * @return array<string,mixed>
     */
    protected function get($key)
    {
        if (!$this->has($key)) {
            return [];
        }

        $pagination = ResponseMediator::getPagination($this->client->getLastResponse());

        /** @var array<string,mixed> */
        return ResponseMediator::getContent($this->client->getHttpClient()->get($pagination[$key]));
    }
}
