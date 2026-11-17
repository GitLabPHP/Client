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

namespace Gitlab\Tests;

use Generator;
use Gitlab\Client;
use Gitlab\ResultPager;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class ResultPagerTest extends TestCase
{
    #[DataProvider('linkProvider')]
    public function testFetchNextRequestsNextPage(string $link, string $expected): void
    {
        $requests = [];
        $responses = [
            new Response(200, ['Content-Type' => 'application/json', 'Link' => '<'.$link.'>; rel="next"'], '[{"id":1}]'),
            new Response(200, ['Content-Type' => 'application/json'], '[{"id":2}]'),
        ];

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient
            ->expects($this->exactly(2))
            ->method('sendRequest')
            ->willReturnCallback(static function (RequestInterface $request) use (&$requests, &$responses): Response {
                $requests[] = $request;

                return \array_shift($responses);
            });

        $client = Client::createWithHttpClient($httpClient);
        $client->setUrl('http://gitlab.internal:8080');
        $pager = new ResultPager($client, 1);

        $pager->fetch($client->projects(), 'all', [['search' => 'my project']]);

        $this->assertSame([['id' => 2]], $pager->fetchNext());
        $this->assertSame($expected, (string) $requests[1]->getUri());
    }

    public static function linkProvider(): Generator
    {
        yield 'other origin' => [
            'https://gitlab.example.com/api/v4/projects?page=2&per_page=1&search=my%20project',
            'http://gitlab.internal:8080/api/v4/projects?page=2&per_page=1&search=my%20project',
        ];

        yield 'path with a colon followed by digits' => [
            'https://gitlab.example.com/api/v4/projects:80/?page=2&per_page=1&search=my%20project',
            'https://gitlab.example.com/api/v4/projects:80/?page=2&per_page=1&search=my%20project',
        ];

        yield 'path beginning with two slashes' => [
            'https://gitlab.example.com//api/v4/projects?page=2&per_page=1&search=my%20project',
            'https://gitlab.example.com//api/v4/projects?page=2&per_page=1&search=my%20project',
        ];
    }
}
