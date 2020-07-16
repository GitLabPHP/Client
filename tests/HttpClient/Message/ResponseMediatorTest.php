<?php

declare(strict_types=1);

namespace Gitlab\Tests\HttpClient\Message;

use Gitlab\Exception\RuntimeException;
use Gitlab\HttpClient\Message\ResponseMediator;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\stream_for;
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
            stream_for('{"foo": "bar"}')
        );

        $this->assertSame(['foo' => 'bar'], ResponseMediator::getContent($response));
    }

    public function testGetContentNotJson()
    {
        $response = new Response(
            200,
            [],
            stream_for('foobar')
        );

        $this->assertSame('foobar', ResponseMediator::getContent($response));
    }

    public function testGetContentInvalidJson()
    {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            stream_for('foobar')
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('json_decode error: Syntax error');

        ResponseMediator::getContent($response);
    }

    public function testGetErrrorMessageInvalidJson()
    {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            stream_for('foobar')
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
