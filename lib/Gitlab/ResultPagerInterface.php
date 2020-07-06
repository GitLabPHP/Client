<?php

namespace Gitlab;

use Gitlab\Api\ApiInterface;

/**
 * This is the result pager interface.
 *
 * @author Ramon de la Fuente <ramon@future500.nl>
 * @author Mitchel Verschoof <mitchel@future500.nl>
 * @author Graham Campbell <graham@alt-three.com>
 */
interface ResultPagerInterface
{
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
    public function fetch(ApiInterface $api, $method, array $parameters = []);

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
    public function fetchAll(ApiInterface $api, $method, array $parameters = []);

    /**
     * Check to determine the availability of a next page.
     *
     * @return bool
     */
    public function hasNext();

    /**
     * Fetch the next page.
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchNext();

    /**
     * Check to determine the availability of a previous page.
     *
     * @return bool
     */
    public function hasPrevious();

    /**
     * Fetch the previous page.
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchPrevious();

    /**
     * Fetch the first page.
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchFirst();

    /**
     * Fetch the last page.
     *
     * @throws \Http\Client\Exception
     *
     * @return array
     */
    public function fetchLast();
}
