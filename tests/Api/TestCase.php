<?php

declare(strict_types=1);

/*
 * This file is part of the Gitlab API library.
 *
 * (c) Matt Humphrey <matth@windsor-telecom.co.uk>
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gitlab\Tests\Api;

use Gitlab\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Client\ClientInterface;

abstract class TestCase extends BaseTestCase
{
    abstract protected function getApiClass(): string;

    protected function getApiMock(array $methods = []): MockObject
    {
        $httpClient = $this->getMockBuilder(ClientInterface::class)
            ->onlyMethods(['sendRequest'])
            ->getMock();
        $httpClient
            ->expects($this->any())
            ->method('sendRequest');

        $client = Client::createWithHttpClient($httpClient);

        return $this->getMockBuilder($this->getApiClass())
            ->onlyMethods(\array_merge(['getAsResponse', 'get', 'post', 'delete', 'put'], $methods))
            ->setConstructorArgs([$client, null])
            ->getMock();
    }
}
