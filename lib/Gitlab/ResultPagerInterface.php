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
     * Fetch a single result (page) from an api call.
     *
     * @param ApiInterface $api        the Api instance
     * @param string       $method     the method name to call on the Api instance
     * @param array        $parameters the method parameters in an array
     *
     * @return array returns the result of the Api::$method() call
     */
    public function fetch(ApiInterface $api, $method, array $parameters = []);

    /**
     * Fetch all results (pages) from an api call.
     *
     * @param ApiInterface $api        the Api instance
     * @param string       $method     the method name to call on the Api instance
     * @param array        $parameters the method parameters in an array
     *
     * @return array returns a merge of the results of the Api::$method() call
     */
    public function fetchAll(ApiInterface $api, $method, array $parameters = []);

    /**
     * Check to determine the availability of a next page.
     *
     * @return bool
     */
    public function hasNext();

    /**
     * Check to determine the availability of a previous page.
     *
     * @return bool
     */
    public function hasPrevious();

    /**
     * Fetch the next page.
     *
     * @return array
     */
    public function fetchNext();

    /**
     * Fetch the previous page.
     *
     * @return array
     */
    public function fetchPrevious();

    /**
     * Fetch the first page.
     *
     * @return array
     */
    public function fetchFirst();

    /**
     * Fetch the last page.
     *
     * @return array
     */
    public function fetchLast();
}
