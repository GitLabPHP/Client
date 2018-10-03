<?php

namespace Gitlab\Tests\HttpClient;

use Gitlab\HttpClient\Builder;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\Plugin;
use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;

/**
 * @author Fabien Bourigault <bourigaultfabien@gmail.com>
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Builder
     */
    private $subject;

    public function setUp()
    {
        $this->subject = new Builder(
            $this->getMockBuilder(HttpClient::class)->getMock(),
            $this->getMockBuilder(RequestFactory::class)->getMock(),
            $this->getMockBuilder(StreamFactory::class)->getMock()
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
        $this->assertInstanceOf(HttpMethodsClient::class, $this->subject->getHttpClient());
    }
}
