<?php

namespace Gitlab\HttpClient\Plugin;

use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;

if (interface_exists(HttpMethodsClientInterface::class)) {
    /**
     * @internal
     */
    trait HistoryTrait
    {
        /**
         * Record a failed call.
         *
         * @param RequestInterface         $request
         * @param ClientExceptionInterface $exception
         *
         * @return void
         */
        public function addFailure(RequestInterface $request, ClientExceptionInterface $exception)
        {
        }
    }
} else {
    /**
     * @internal
     */
    trait HistoryTrait
    {
        /**
         * Record a failed call.
         *
         * @param RequestInterface $request
         * @param Exception        $exception
         *
         * @return void
         */
        public function addFailure(RequestInterface $request, Exception $exception)
        {
        }
    }
}
