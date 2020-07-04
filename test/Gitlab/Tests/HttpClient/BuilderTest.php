<?php

declare(strict_types=1);

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
    public function initBuilder()
    {
        $this->subject = new Builder(
            $this->getMockBuilder(ClientInterface::class)->getMock(),
            $this->getMockBuilder(RequestFactoryInterface::class)->getMock(),
            $this->getMockBuilder(StreamFactoryInterface::class)->getMock()
        );
    }

    public function testAddPluginShouldInvalidateHttpClient()
    {
        $client = $this->subject->getHttpClient();

        $this->subject->addPlugin($this->getMockBuilder(Plugin::class)->getMock());

        $this->assertNotSame($client, $this->subject->getHttpClient());
    }

    public function testRemovePluginShouldInvalidateHttpClient()
    {
        $this->subject->addPlugin($this->getMockBuilder(Plugin::class)->getMock());

        $client = $this->subject->getHttpClient();

        $this->subject->removePlugin(Plugin::class);

        $this->assertNotSame($client, $this->subject->getHttpClient());
    }

    public function testHttpClientShouldBeAnHttpMethodsClient()
    {
        $this->assertInstanceOf(HttpMethodsClientInterface::class, $this->subject->getHttpClient());
    }
}
