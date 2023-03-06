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

namespace Gitlab\Tests\HttpClient\Message;

use Gitlab\Exception\RuntimeException;
use Gitlab\HttpClient\Message\ResponseMediator;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 */
class ResponseMediatorTest extends TestCase
{
    public function testGetContent(): void
    {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            Utils::streamFor('{"foo": "bar"}')
        );

        $this->assertSame(['foo' => 'bar'], ResponseMediator::getContent($response));
    }

    public function testGetContentNotJson(): void
    {
        $response = new Response(
            200,
            [],
            Utils::streamFor('foobar')
        );

        $this->assertSame('foobar', ResponseMediator::getContent($response));
    }

    public function testGetContentInvalidJson(): void
    {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            Utils::streamFor('foobar')
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('json_decode error: Syntax error');

        ResponseMediator::getContent($response);
    }

    public function testGetErrrorMessageInvalidJson(): void
    {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            Utils::streamFor('foobar')
        );

        $this->assertNull(ResponseMediator::getErrorMessage($response));
    }

    public function testGetPagination(): void
    {
        $header = '<https://example.gitlab.com>; rel="first",<https://example.gitlab.com>; rel="next",<https://example.gitlab.com>; rel="prev",<https://example.gitlab.com>; rel="last"';

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
