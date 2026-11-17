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
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\HttpClient\HttplugClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @author Fabien Bourigault <bourigaultfabien@gmail.com>
 */
class BuilderTest extends TestCase
{
    private Builder $subject;

    #[Before]
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

    public function testSymfonyPsr18ClientShouldNotFollowRedirects(): void
    {
        $maxRedirects = null;

        $httpClient = new MockHttpClient(static function (string $method, string $url, array $options) use (&$maxRedirects): MockResponse {
            $maxRedirects = $options['max_redirects'];

            return new MockResponse();
        });

        $builder = new Builder(new Psr18Client($httpClient));
        $builder->getHttpClient()->get('https://gitlab.com/api/v4/version');

        $this->assertSame(0, $maxRedirects);
    }

    public function testSymfonyHttplugClientShouldNotFollowRedirects(): void
    {
        $maxRedirects = null;

        $httpClient = new MockHttpClient(static function (string $method, string $url, array $options) use (&$maxRedirects): MockResponse {
            $maxRedirects = $options['max_redirects'];

            return new MockResponse();
        });

        $builder = new Builder(new HttplugClient($httpClient));
        $builder->getHttpClient()->get('https://gitlab.com/api/v4/version');

        $this->assertSame(0, $maxRedirects);
    }

    public function testStreamFactoryShouldBeAStreamFactory(): void
    {
        $this->assertInstanceOf(StreamFactoryInterface::class, $this->subject->getStreamFactory());
    }
}
