<?php

namespace Gitlab\Tests;

use Gitlab\Api\ApiInterface;
use Gitlab\Client;
use Gitlab\ResultPager;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;
use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\HttpMethodsClientInterface;
use PHPUnit\Framework\TestCase;

class ResultPagerTest extends TestCase
{
    public function testFetch()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $api = $this->getMockBuilder(ApiInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__construct', 'all'])
            ->getMock()
        ;
        $api->expects($this->once())
            ->method('all')
            ->willReturn(['project1', 'project2'])
        ;

        $pager = new ResultPager($client);

        $result = $pager->fetch($api, 'all');

        $this->assertEquals(['project1', 'project2'], $result);
    }

    public function testFetchAll()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $response1 = (new Response())->withHeader('Link', '<https://example.gitlab.com/projects?page=2>; rel="next",');
        $response2 = (new Response())->withHeader('Link', '<https://example.gitlab.com/projects?page=3>; rel="next",')
            ->withHeader('Content-Type', 'application/json')
            ->withBody(stream_for('["project3", "project4"]'))
        ;
        $response3 = (new Response())->withHeader('Content-Type', 'application/json')
            ->withBody(stream_for('["project5", "project6"]'))
        ;

        $client
            ->method('getLastResponse')
            ->will($this->onConsecutiveCalls(
                $response1,
                $response1,
                $response1,
                $response2,
                $response2,
                $response2,
                $response3
            ))
        ;

        if (interface_exists(HttpMethodsClientInterface::class)) {
            $httpClient = $this->createMock(HttpMethodsClientInterface::class);
        } else {
            $httpClient = $this->getMockBuilder(HttpMethodsClient::class)
                ->disableOriginalConstructor()
                ->getMock()
            ;
        }

        $httpClient->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                ['https://example.gitlab.com/projects?page=2'],
                ['https://example.gitlab.com/projects?page=3']
            )
            ->will($this->onConsecutiveCalls(
                $response2,
                $response3
            ))
        ;

        $client
            ->method('getHttpClient')
            ->willReturn($httpClient)
        ;

        $api = $this->getMockBuilder(ApiInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['__construct', 'all'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('all')
            ->willReturn(['project1', 'project2'])
        ;

        $pager = new ResultPager($client);

        $result = $pager->fetchAll($api, 'all');

        $this->assertEquals(['project1', 'project2', 'project3', 'project4', 'project5', 'project6'], $result);
    }
}
