<?php

namespace Gitlab;

use Gitlab\Api\ApiInterface;
use Gitlab\Exception\RuntimeException;
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
     * The pagination result from the API.
     *
     * @var array<string,string>
     */
    protected $pagination;

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
        $this->pagination = [];
    }

    /**
     * Fetch a single result from an api call.
     *
     * @param ApiInterface $api
     * @param string       $method
     * @param array        $parameters
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetch(ApiInterface $api, $method, array $parameters = [])
    {
        $result = $api->$method(...array_values($parameters));

        if (!is_array($result)) {
            throw new RuntimeException('Pagination of endpoints that produce blobs is not supported.');
        }

        $this->postFetch();

        return $result;
    }

    /**
     * Fetch all results from an api call.
     *
     * @param ApiInterface $api
     * @param string       $method
     * @param array        $parameters
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchAll(ApiInterface $api, $method, array $parameters = [])
    {
        $result = $this->fetch($api, $method, $parameters);

        while ($this->hasNext()) {
            $result = array_merge($result, $this->fetchNext());
        }

        return $result;
    }

    /**
     * Check to determine the availability of a next page.
     *
     * @return bool
     */
    public function hasNext()
    {
        return isset($this->pagination['next']);
    }

    /**
     * Fetch the next page.
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchNext()
    {
        return $this->get('next');
    }

    /**
     * Check to determine the availability of a previous page.
     *
     * @return bool
     */
    public function hasPrevious()
    {
        return isset($this->pagination['prev']);
    }

    /**
     * Fetch the previous page.
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchPrevious()
    {
        return $this->get('prev');
    }

    /**
     * Fetch the first page.
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchFirst()
    {
        return $this->get('first');
    }

    /**
     * Fetch the last page.
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchLast()
    {
        return $this->get('last');
    }

    /**
     * Refresh the pagination property.
     *
     * @return void
     */
    protected function postFetch()
    {
        $response = $this->client->getLastResponse();

        if (null === $response) {
            $this->pagination = [];
        } else {
            $this->pagination = ResponseMediator::getPagination($response);
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     *
     * @deprecated since version 9.18 and will be removed in 10.0. Use the hasNext() or hasPrevious() methods instead.
     */
    protected function has($key)
    {
        @trigger_error(sprintf('The %s() method is deprecated since version 9.18 and will be removed in 10.0. Use the hasNext() or hasPrevious() methods instead.', __METHOD__), E_USER_DEPRECATED);

        return isset($this->pagination[$key]);
    }

    /**
     * @param string $key
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    protected function get($key)
    {
        $pagination = isset($this->pagination[$key]) ? $this->pagination[$key] : null;

        if (null === $pagination) {
            return [];
        }

        $result = $this->client->getHttpClient()->get($pagination);

        $content = ResponseMediator::getContent($result);

        if (!is_array($content)) {
            throw new RuntimeException('Pagination of endpoints that produce blobs is not supported.');
        }

        $this->postFetch();

        return $content;
    }
}
