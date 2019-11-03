<?php

namespace Gitlab\HttpClient\Plugin;

use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\RequestInterface;

if (interface_exists(HttpMethodsClientInterface::class)) {
    trait HistoryTrait
    {
        public function addFailure(RequestInterface $request, ClientExceptionInterface $exception)
        {
        }
    }
} else {
    trait HistoryTrait
    {
        public function addFailure(RequestInterface $request, Exception $exception)
        {
        }
    }
}
