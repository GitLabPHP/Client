<?php

namespace Gitlab\Tests\HttpClient\Plugin;

use Gitlab\HttpClient\Plugin\ApiVersion;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Http\Client\Promise\HttpFulfilledPromise;
use Psr\Http\Message\RequestInterface;
use PHPUnit\Framework\TestCase;

class ApiVersionTest extends TestCase
{
    public function testCallNextCallback()
    {
        $request = new Request('GET', '');
        $plugin = new ApiVersion();
        $promise = new HttpFulfilledPromise(new Response());

        $callback = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['next'])
            ->getMock()
        ;
        $callback->expects($this->once())
            ->method('next')
            ->with($this->isInstanceOf(RequestInterface::class))
            ->willReturn($promise)
        ;

        $this->assertEquals($promise, $plugin->handleRequest($request, [$callback, 'next'], function () {
        }));
    }

    public function testPrefixRequestPath()
    {
        $request = new Request('GET', 'projects');
        $expected = new Request('GET', '/api/v4/projects');
        $plugin = new ApiVersion();
        $promise = new HttpFulfilledPromise(new Response());

        $callback = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['next'])
            ->getMock()
        ;
        $callback->expects($this->once())
            ->method('next')
            ->with($expected)
            ->willReturn($promise)
        ;

        $plugin->handleRequest($request, [$callback, 'next'], function () {
        });
    }

    public function testNoPrefixingRequired()
    {
        $request = new Request('GET', '/api/v4/projects');
        $plugin = new ApiVersion();
        $promise = new HttpFulfilledPromise(new Response());

        $callback = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['next'])
            ->getMock()
        ;
        $callback->expects($this->once())
            ->method('next')
            ->with($request)
            ->willReturn($promise)
        ;

        $plugin->handleRequest($request, [$callback, 'next'], function () {
        });
    }
}
