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

namespace Gitlab\Tests\HttpClient;

use Gitlab\HttpClient\Builder;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * @author Fabien Bourigault <bourigaultfabien@gmail.com>
 */
class BuilderTest extends TestCase
{
    /**
     * @var Builder
     */
    private $subject;

    /**
     * @before
     */
    public function initBuilder(): void
    {
        $this->subject = new Builder(
            $this->createMock(ClientInterface::class),
            $this->createMock(RequestFactoryInterface::class),
            $this->createMock(StreamFactoryInterface::class)
        );
    }

    public function testAddPluginShouldInvalidateHttpClient(): void
    {
        $client = $this->subject->getHttpClient();

        $this->subject->addPlugin($this->createMock(Plugin::class));

        $this->assertNotSame($client, $this->subject->getHttpClient());
    }

    public function testRemovePluginShouldInvalidateHttpClient(): void
    {
        $this->subject->addPlugin($this->createMock(Plugin::class));

        $client = $this->subject->getHttpClient();

        $this->subject->removePlugin(Plugin::class);

        $this->assertNotSame($client, $this->subject->getHttpClient());
    }

    public function testHttpClientShouldBeAnHttpMethodsClient(): void
    {
        $this->assertInstanceOf(HttpMethodsClientInterface::class, $this->subject->getHttpClient());
    }

    public function testStreamFactoryShouldBeAStreamFactory(): void
    {
        $this->assertInstanceOf(StreamFactoryInterface::class, $this->subject->getStreamFactory());
    }
}
