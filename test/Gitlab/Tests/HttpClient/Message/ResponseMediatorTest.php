<?php

declare(strict_types=1);

namespace Gitlab\Tests\HttpClient\Message;

use Gitlab\HttpClient\Message\ResponseMediator;
use Gitlab\Exception\RuntimeException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Graham Campbell <graham@alt-three.com>
 */
class ResponseMediatorTest extends TestCase
{
    public function testGetContent()
    {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            \GuzzleHttp\Psr7\stream_for('{"foo": "bar"}')
        );

        $this->assertSame(['foo' => 'bar'], ResponseMediator::getContent($response));
    }

    public function testGetContentNotJson()
    {
        $body = 'foobar';
        $response = new Response(
            200,
            [],
            \GuzzleHttp\Psr7\stream_for($body)
        );

        $this->assertSame($body, ResponseMediator::getContent($response));
    }

    public function testGetContentInvalidJson()
    {
        $body = 'foobar';
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            \GuzzleHttp\Psr7\stream_for($body)
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('json_decode error: Syntax error');

        ResponseMediator::getContent($response);
    }

    public function testGetErrrorMessageInvalidJson()
    {
        $body = 'foobar';
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            \GuzzleHttp\Psr7\stream_for($body)
        );

        $this->assertNull(ResponseMediator::getErrorMessage($response));
    }

    public function testGetPagination()
    {
        $header = <<<'TEXT'
<https://example.gitlab.com>; rel="first",
<https://example.gitlab.com>; rel="next",
<https://example.gitlab.com>; rel="prev",
<https://example.gitlab.com>; rel="last",
TEXT;

        $pagination = [
            'first' => 'https://example.gitlab.com',
            'next' => 'https://example.gitlab.com',
            'prev' => 'https://example.gitlab.com',
            'last' => 'https://example.gitlab.com',
        ];

        // response mock
        $response = new Response(200, ['link' => $header]);
        $result = ResponseMediator::getPagination($response);

        $this->assertSame($pagination, $result);
    }
}
